<?php 
// TODO: Clean this mess! rename it to leumFront?
require_once 'core/leum-core.php';
require_once 'functions.php';
require_once 'dispatcher.php';
require_once 'front.php';

$core = new LeumCore();
$leum = new Leum();
class Leum
{
	private static $_instance = null;
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

	public $pageClass;

	private $titleBar;

	public function __construct()
	{
		// Set the instance variable for singleton, get a database object and initialize the dispatcher.
		self::$_instance = $this;
		$this->dbc = LeumCore::$dbc;

		$this->defaultRole = Role::GetSingle($this->dbc, DEFAULT_ROLE, true);

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

		$this->Init();

		// Show the 404 page if we have no route/page.
		if(!isset($this->routeResolve) || !is_file(SYS_ROOT . "/leum/$this->routeResolve"))
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
		// Add the routes defined in routes.conf.php.
		FrontRoutes();

		// Trigger a hook for others to use.
		LeumCore::InvokeHook("leum.front.routes");

		$route = Dispatcher::ResolveRoute($this->request);

		if($route['state'] == Dispatcher::FOUND)
		{
			$this->routeResolve = $route['target'];
			$this->arguments = array_merge($route['params'], $this->arguments);
		}
		else
			$this->routeResolve = null;
	}
	private function Init()
	{
		$menu = Front::GetWidget('menu', ['items' => PRIMARY_MENU]);
		// Create the title_bar.
		$this->titleBar = Front::GetWidget('title_bar', ['menu' => $menu]);
		// $this->titleBar->menu = 
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
		$pageFile = SYS_ROOT . "/leum/$pageFile";
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

		// If the page is not null leave. Looks like another page was loaded first.
		// Most likely an error.
		if($this->page != null)
			return;

		$this->page = $newPage;
		$this->pageClass = $pageClass;
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
		
		echo "<!-- leum.front.head hook -->\n";
		LeumCore::InvokeHook('leum.front.head');
	}

	// Tells the page to show it's output.
	public function Output()
	{
		// the title_bar widget.
		$this->titleBar->Show();

		// the page's content.
		$this->page->Content();

		// the footer.
		echo "<!-- leum.front.footer hook -->\n";
		LeumCore::InvokeHook('leum.front.footer');

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

		$this->LoadPage("pages/error-pages/$class.php", true);
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
?>
