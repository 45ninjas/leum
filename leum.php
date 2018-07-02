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

	public $title = APP_TITLE;
	public $request = "";
	public $arguments;

	private $dbc;

	public $headIncludes;

	public $user = null;

	public function __construct()
	{
		// Routes variable from preferences.php
		global $routes;

		// Set the instance variable for singleton, get a database object and initialize the dispatcher.
		self::$_instance = $this;
		$this->GetDatabase();
		$this->dispatcher = new Dispatcher($routes);

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

		// Get the request and remove the trailing slash.
		if(isset($_GET['request']))
		{
			$this->request = $_GET['request'];

			// Remove that pesky trailing slash.
			if(substr($this->request,-1) == '/')
				$this->request = substr($this->request, 0, -1);
		}

		// Setup the head includes.
		$this->headIncludes = array();

		// Get the page and arguments form the dispatcher.
		$arguments = $this->dispatcher->GetPage($this->request);

		// Show the 404 page if we have no route/page.
		if($arguments[0] == false)
		{
			self::Show404Page();
			return;
		}

		// Include the code for the page.
		$pageFile = array_shift($arguments);
		$pageClass = basename($pageFile, ".php");

		// Show a 500 page if the file does not exist.
		if(!is_file($pageFile))
		{
			self::ShowErrorPage(500, "The file containing the '$pageClass' class for this page does not exist. [Routes Issue]");
			return;
		}

		include $pageFile;
		// Show a 500 page if the class does not exist.
		if(!class_exists($pageClass))
		{
			self::ShowErrorPage(500, "The '$pageClass' class for this page does not exist. [Routes Issue]");
			return;
		}
		// Initialize the page and set the arguments.
		$this->page = new $pageClass($this, $this->dbc, $this->user, $arguments);
		$this->arguments = $arguments;
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
		http_response_code($code);

		$file = SYS_ROOT . "/pages/error-pages/$class.php";

		if(!is_file($file))
			throw new Exception("Error Page '$class' does not exist");

		require_once $file;

		$this->arguments['error-message'] = $message;
		$this->arguments['error-code'] = $code;
		$this->page = new $class($this, $this->dbc, null, $this->arguments);
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
	public function AttemptLogin($username, $password)
	{
		if(User::CheckPassword($this->dbc, $username, $password))
		{
			$user = User::GetSingle($this->dbc, $username);
			$user->Login($this->dbc);
			$user->GetPermissions($this->dbc);
			$_SESSION['user_id'] = $user->user_id;
			$_SESSION['name'] = $user->username;
			$_SESSION['permissions'] = $user->permissions;

			return true;
		}
		return false;
	}
	public function Logout()
	{
		session_destroy();
		$_SESSION = array();
	}
	public function AllowedTo($permissionSlug)
	{
		if(!isset($this->user))
			return false;
		return $this->user->HasPermission($permissionSlug);
	}
}

interface IPage
{
	public function __construct($leum, $dbc, $userInfo, $arguments);
	public function Content();
}
?>
