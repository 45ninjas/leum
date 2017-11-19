<?php 
require_once 'prefrences.php';
require_once 'functions.php';
require_once 'dispatcher.php';
$leum = new Leum();

class Leum
{
	private static $_instance = null;
	public $dispatcher;
	public $page;

	public $title = APP_TITLE;
	public $request = "";
	public $arguments;

	private $my_db;

	public static function Instance()
	{
		if(self::$_instance == null)
			die("Error: Leum has not been instantiated properly. Or a function is trying to access Leum too early.");

		return self::$_instance;
	}

	public function GetDatabase()
	{
		if(!isset($my_db))
			$this->my_db = DBConnect();
		return $this->my_db;
	}
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

		// Setup the dispatcher.
		global $routes;
		$this->dispatcher = new Dispatcher($routes);

		// Get the page from the dispatcher.
		$arguments = $this->dispatcher->GetPage($this->request);

		$pageFile = array_shift($arguments);
		
		include $pageFile;
		$this->page = new Page($arguments);
		$this->arguments = $arguments;
	}

	public function Head()
	{

	}
	public function Output()
	{
		//$this->Debug();
		$this->page->Content();
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
