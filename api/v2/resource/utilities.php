<?php namespace API;
class Utilities
{
	private $dbc;
	private $api;
	private $invalidInput;
	function __construct($api)
	{
		$this->invalidInput = new \Exception("Invalid Input(s), Index needs to be a number grater or equal to zero");
		$this->api = $api;
		$this->dbc = $api->dbc;
	}
	public function GET()
	{
		$firstArg = array_shift($this->api->args);
		if($firstArg)
			return $this->Directories();
		else
			throw $this->invalidInput;

		//return $data;
	}
	private function Directories()
	{
		$startDirectory = SYS_ROOT . MEDIA_DIR;
		
		if(isset($_GET['dir']))
		{
			$dir = $_GET['dir'];
			$dir = str_replace('\\', '/', $dir);
			$dir = trim($dir, '/');

			$globString = "$startDirectory/$dir/*";
		}
		else
			$globString = "$startDirectory/*";
		
		$dirs = glob($globString, GLOB_ONLYDIR | GLOB_MARK);
		foreach ($dirs as $key => $value)
		{
			// Replace all backslashes with forward ones and remove the start directory from it.
			$dirs[$key] = substr(str_replace('\\', '/', $value), strlen($startDirectory) + 1);
		}
		if(count($dirs) == 0)
			return null;
		return $dirs;
	}
}
 ?>