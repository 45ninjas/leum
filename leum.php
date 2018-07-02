<?php 
define('SYS_ROOT', __DIR__);
require_once 'preferences.php';
require_once 'functions.php';
require_once 'dispatcher.php';
require_once SYS_ROOT . '/core/leum-core.php';
$leum = new Leum();

class Leum
{
	private static $_instance = null;
	public $dispatcher;
	public $page;
	public $errorClass;

	public $title = APP_TITLE;
	public $request = "";
	public $arguments = array();
	public $routeResolve = null;

	private $dbc;

	public $headIncludes = array();

	public $user = null;

	public function __construct()
	{
		// Routes variable from preferences.php
		global $routes;

		// Set the instance variable for singleton, get a database object and initialize the dispatcher.
		self::$_instance = $this;
		$this->GetDatabase();
		$this->dispatcher = new Dispatcher($routes);

		// Get the request and remove the trailing slash.
		if(isset($_GET['request']))
		{
			$this->request = $_GET['request'];

			// Remove that pesky trailing slash.
			if(substr($this->request,-1) == '/')
				$this->request = substr($this->request, 0, -1);
		}

		$this->Dispatch();

		$this->UserInit();

		// Show the 404 page if we have no route/page.
		if(!isset($this->routeResolve) || !is_file(SYS_ROOT . "/pages/$this->routeResolve"))
		{
			self::Show404Page();
			return;
		}

		// If no page has been set then, use the page the dispatcher found.
		if(!isset($this->page))
			$this->LoadPage($this->routeResolve);
	}
	private function Dispatch()
	{
		$dispResult = $this->dispatcher->GetPage($this->request);
		if($dispResult != false)
		{
			$this->routeResolve = array_shift($dispResult);
			$this->arguments = array_merge($dispResult, $this->arguments);
		}
		else
			$this->routeResolve = null;

	}
	private function UserInit()
	{
		// Setup the session parameters
		session_set_cookie_params(0,ROOT); 
		session_name("leum");
		session_start();

		// Does the user want to logout?
		if(isset($_GET['logout']))
			$this->Logout();

		// Is there a user session?
		if(isset($_SESSION['user_id']))
		{
			// Get the user data.
			$this->user = User::GetSingle($this->dbc, $_SESSION['user_id'], true);
			if($this->user == false)
			{
				$this->user = null;
				throw new Exception("Unable to get user from session");
			}
		}

		// Disallow access to the app, force login. Unless allowed to.
		if($this->routeResolve != LOGIN_PAGE && !$this->AllowedTo("access-app"))
		{
			$location = "/" . LOGIN_URL;

			if(!empty($this->request))
				$location .= "?req=$this->request";

			$this->Redirect($location);
		}

	}

	public function LoadPage($pageFile, $throw = false)
	{
		$pageFile = SYS_ROOT . "/pages/$pageFile";
		$pageClass = basename($pageFile, ".php");
		// Show a 500 page if the file does not exist.
		if(!is_file($pageFile))
		{
			$error = "The file containing the '$pageClass' class for this page does not exist. [Routes Issue]";
			if($throw)
				throw new Exception($error);
			else
				$this->ShowErrorPage(500, $error);
			return;
		}

		include $pageFile;
		// Show a 500 page if the class does not exist.
		if(!class_exists($pageClass))
		{
			$error = "The '$pageClass' class for this page does not exist. [Routes Issue]";
			if($throw)
				throw new Exception($error);
			else
				$this->ShowErrorPage(500, $error);
			return;
		}
		// Initialize the page and set the arguments.
		$newPage = new $pageClass($this, $this->dbc, $this->user, $this->arguments);

		// Just in-case a new page was created during the creating of this page. Don't overwrite the new page. As it's very likely an error.
		if($this->page == null)
			$this->page = $newPage;
	}

	// Similar idea to how enqueue works in wordpress. 
	public function RequireResource($file, $html, $head = true)
	{
		if(!array_key_exists($file, $this->headIncludes))
			$this->headIncludes[$file] = $html;
	}

	// Singleton Instance.
	public static function Instance()
	{
		if(self::$_instance == null)
			die("Error: Leum has not been instantiated properly. Or a function is trying to access Leum too early.");

		return self::$_instance;
	}

	// Get a PDO database object (aka: dbc). This is now Deprecated.
	public function GetDatabase()
	{
		if(!isset($this->dbc))
			$this->dbc = DBConnect();
		return $this->dbc;
	}

	public function Head()
	{
		if(count($this->headIncludes) > 0)
		{
			echo "<!-- RequireResource resources -->\n";
			foreach ($this->headIncludes as $file => $html)
			{
				echo "$html\n";
			}
			echo "\n";
		}
	}

	// Tells the page to show it's output.
	public function Output()
	{
		$this->page->Content();
	}
	// Shows triggers the 404 page to show. can provide a custom message.
	public function Show404Page($message = null)
	{
		$this->ShowErrorPage(404, $message, 'error_404');
	}
	public function ShowPermissionErrorPage($message = null)
	{
		$this->ShowErrorPage(403, $message, 'no_permission');
	}
	public function ShowErrorPage($code, $message, $class = 'error_generic')
	{
		$this->arguments['error-code'] = $code;
		$this->arguments['error-message'] = $message;
		$this->page = null;
		http_response_code($code);		

		$this->LoadPage("error-pages/$class.php", true);
	}
	// Sets the title of the page. Force bypasses prefix and suffix from config.
	public function SetTitle($newTitle, $force = false)
	{
		$this->title = $newTitle;

		if(!$force && defined('TITLE_PREFIX'))
			$this->title = TITLE_PREFIX . $newTitle;

		if(!$force && defined('TITLE_SUFFIX'))
			$this->title .= TITLE_SUFFIX;
	}
	public function AttemptLogin($username, $password, &$message)
	{
		// Did the user enter a correct password?
		if(User::CheckPassword($this->dbc, $username, $password))
		{
			// Get the user.
			$user = User::GetSingle($this->dbc, $username, true);

			// Is the user allowed to login?
			if(!$user->HasPermissions(["login"]))
			{
				$message="You don't have permission to login. Your account could have been suspended/locked. Contact your administrator for assistance.";
				return false;
			}

			// Actually log the user in.
			$user->Login($this->dbc);
			$this->user = $user;
			$_SESSION['user_id'] = $user->user_id;

			// Redirect the user back to the page they where trying to access.
			$redirect = "";
			if(isset($_GET['req']))
				$redirect = $_GET['req'];
			$this->Redirect($_GET['req']);

			return true;
		}
		// User name or password was wrong.
		$message= "The provided user-name or password didn't match any of our records.";
		return false;
	}
	public function Logout()
	{
		session_destroy();
		$_SESSION = array();
	}
	public function AllowedTo($permissionSlugs)
	{
		if(is_string($permissionSlugs))
			$permissionSlugs = [$permissionSlugs];

		// echo "Checking Permissions: " . implode(', ', $permissionSlugs) . PHP_EOL;

		if(!isset($this->user))
			return false;

		return $this->user->HasPermissions($permissionSlugs);
	}
	public function PermissionCheck(...$permissions)
	{
		if(!$this->AllowedTo($permissions))
		{
			$this->ShowPermissionerrorPage("You don't have sufficient permissions");
		}
	}
	public function Redirect($targetRequest)
	{
		// TODO: Follow the HTML spec. and use a full url.
		$targetRequest = ROOT . $targetRequest;
		header("Location: $targetRequest");

		?>
		<!DOCTYPE html>
		<html>
		<head>
			<title><?=APP_TITLE?> Redirect</title>
		</head>
		<body>
			<p>Redirecting. This will only take a moment. Click <a href="<?=$targetRequest?>">here</a> if it's not working.</p>
		</body>
		</html>
		<?php
		die();
	}
}

interface IPage
{
	public function __construct($leum, $dbc, $userInfo, $arguments);
	public function Content();
}
?>
