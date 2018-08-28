<?php 
require_once 'core/leum-core.php';
require_once 'functions.php';
require_once 'dispatcher.php';
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

	public $dbc;

	public $headIncludes = array();

	public $user = null;
	public $defaultRole;

	public $messages;

	public $gitStatus;

	public function __construct()
	{
		// Routes variable from preferences.php
		global $routes;

		// Set the instance variable for singleton, get a database object and initialize the dispatcher.
		self::$_instance = $this;
		$this->GetDatabase();
		$this->dispatcher = new Dispatcher($routes);

		$this->defaultRole = Role::GetSingle($this->dbc, DEFAULT_ROLE, true);

		// Get the request and remove the trailing slash.
		if(isset($_GET['request']))
		{
			$this->request = $_GET['request'];

			// Remove that pesky trailing slash.
			if(substr($this->request,-1) == '/')
				$this->request = substr($this->request, 0, -1);
		}
		$this->Init();
		$this->Dispatch();

		$this->UserInit();

		// Show the 404 page if we have no route/page.
		if(!isset($this->routeResolve) || !is_file(SYS_ROOT . "/leum/pages/$this->routeResolve"))
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
	private function Init()
	{
		include_once 'page-parts/git-status.php';
		$this->gitStatus = new GitStatus(false);
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
			$this->user = User::GetSingle($this->dbc, $_SESSION['user_id'], true, true);
			if($this->user == false)
			{
				$this->user = null;
				throw new Exception("Unable to get user from session");
			}
		}

		// Can we access the app?
		if(!$this->AllowedTo("access-app"))
		{
			// If a user is logged in, let them know what's up.
			if(isset($this->user))
			{
				$this->ShowPermissionerrorPage("You don't have permission to access ". APP_TITLE .".");
				// Message::Create("msg-red", "If you believe you should have permission to access this page please contact your administrator.");
			}
			// Looks like this user has not authenticated, the the thing.
			elseif($this->routeResolve != LOGIN_PAGE && $this->routeResolve != REGISTER_PAGE)
			{
				$location = "/" . LOGIN_URL;

				// If they where trying to access a page include it in the req parameter.
				if(!empty($this->request))
					$location .= "?req=$this->request";

				// Redirect the user to the login page.
				$this->Redirect($location);
			}
		}
	}

	public function LoadPage($pageFile, $throw = false)
	{
		$pageFile = SYS_ROOT . "/leum/pages/$pageFile";
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

		echo "<div class=\"content foot-note\">";
		if($this->gitStatus->valid)
			echo "<p>". $this->gitStatus->GetMessage() . "</p>";
		echo "<p>Leum pre-alpha<br>" . $_SERVER['SERVER_SOFTWARE'] . "</p>";
		echo "</div>";
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
	public function SetTitle($title, $force = false)
	{
		if($force)
			$this->title = $title;
		else
		{
			$args = array
			(
				'%title'	=> $title,
				'%appTitle'	=> APP_TITLE
			);
			$this->title = str_replace(array_keys($args), array_values($args), TITLE_FORMAT);
		}
	}
	public function AttemptLogin($username, $password, &$message)
	{
		// Did the user enter a correct password for the provided user?
		if(UserAccount::Login($this->dbc, $username, $password))
		{
			// Get the user.
			$user = User::GetSingle($this->dbc, $username, true);

			// Is the user allowed to login?
			if(!$user->HasPermissions(["login"]))
			{
				$message="You don't have permission to login. Your account could have been suspended/locked. Contact your administrator for assistance.";
				return false;
			}

			// Set the user variable.
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
			$return = $this->defaultRole->HasPermissions($permissionSlugs);
		else
			$return = $this->user->HasPermissions($permissionSlugs);

		if(!$return)
			$this->arguments['error-permission'] = implode(', ', $permissionSlugs);

		return $return;
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

class Message
{
	public static $messages = array();
	public static function Create($class, $text, $location = "default")
	{
		$msg = new Message($class, $text);
		$msg->AddMessage($location);
	}
	public static function ShowMessages($location = "default", $class = "")
	{
		if(isset(self::$messages[$location]))
		{
			echo "<div class=\"messages $location $class\">";
			foreach (self::$messages[$location] as $message)
			{
				echo "<div class=\"msg $message->class\">$message->text</div>";
			}
			echo "</div>";
		}
	}
	private function AddMessage($location = "default")
	{
		self::$messages[$location][] = $this;
	}
	public $class;
	public $text;
	public function __construct($class, $text)
	{
		$this->class = $class;
		$this->text = $text;
	}
}
?>
