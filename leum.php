<?php 
define('SYS_ROOT', __DIR__);
require_once 'prefrences.php';
require_once 'functions.php';
require_once 'dispatcher.php';
$leum = new Leum();

class Leum
{
	private static $_instance = null;
	public $dispatcher;
	public $page;
	public $error = null;

	public $title = APP_TITLE;
	public $request = "";
	public $arguments;

	private $dbc;

	public $headIncludes;

	public function __construct()
	{
		// Routes variable from preferences.php
		global $routes;

		// Set the instance variable for singleton, get a database object and initialize the dispatcher.
		self::$_instance = $this;
		$this->GetDatabase();
		$this->dispatcher = new Dispatcher($routes);

		// Get the user session and permissions
		// TODO: Implement users, permissions and authentication.
		$userInfo = null;

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

		$pageFile = array_shift($arguments);

		// Include the code for the page.		
		include $pageFile;

		// Initialize the page and set the arguments.
		$this->page = new Page($this, $this->dbc, $userInfo, $arguments);
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
		// If there is an error set, then show the error.
		if(isset($this->error))
		{
			include SYS_ROOT . "/pages/error-pages/404.php";
			$this->page = new Page($this, $this->dbc, null, [$this->error]);
			$this->arguments = [$this->error];
		}
		
		$this->page->Content();
	}

	// Shows triggers the 404 page to show. can provide a custom message.
	public function Show404Page($message = null)
	{
		if(isset($message))
			$this->error = $message;
		else
			$this->error = true;
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
}
?>
