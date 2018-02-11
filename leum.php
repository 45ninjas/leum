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

	private $my_db;

	public $headIncludes;

	public function __construct()
	{
		// Set the instance variable for singleton.
		self::$_instance = $this;

		// Get the request and remove the trailing slash.
		if(isset($_GET['request']))
		{
			$this->request = $_GET['request'];

			if(substr($this->request,-1) == '/')
			{
				$this->request = substr($this->request, 0, -1);
			}
		}

		// Setup the head includes.
		$this->headIncludes = array();

		// Setup the dispatcher.
		global $routes;
		$this->dispatcher = new Dispatcher($routes);

		// Get the page from the dispatcher.
		$arguments = $this->dispatcher->GetPage($this->request);

		if($arguments == false)
		{
			self::Show404Page();
			return;
		}

		$pageFile = array_shift($arguments);
		
		include $pageFile;
		$this->page = new Page($arguments);
		$this->arguments = $arguments;
	}

	public function RequireResource($file, $html)
	{
		if(!array_key_exists($file, $this->headIncludes))
		{
			$this->headIncludes[$file] = $html;
		}
	}

	public static function Instance()
	{
		if(self::$_instance == null)
			die("Error: Leum has not been instantiated properly. Or a function is trying to access Leum too early.");

		return self::$_instance;
	}

	public function GetDatabase()
	{
		if(!isset($this->my_db))
			$this->my_db = DBConnect();
		return $this->my_db;
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
	public function Output()
	{
		if(isset($this->error))
		{
			include SYS_ROOT . "/pages/404.php";
			$this->page = new Error404([$this->error]);
			$this->arguments = [$this->error];
		}
		
		$this->page->Content();
	}
	public function Show404Page($message = null)
	{
		if(isset($message))
			$this->error = $message;
		else
			$this->error = true;
	}
	private function Debug()
	{
		?>
		<div class="debug-head">
			<div class="content">
				<pre>
Leum Debug Information.
Page Title		:<?php echo $this->page->title?>

Request			:<?php echo $this->request; ?>

Arguments		:'<?php echo implode("', '", $this->arguments); ?>'
				</pre>
			</div>
		</div>
		<?php
	}
}
?>
