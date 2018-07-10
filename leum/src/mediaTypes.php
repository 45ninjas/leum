<?php
class MediaTypes
{
	$types
	function __construct()
	{
	}

	public function GetTypes()
	{
		foreach (new DirectoryIterator(SYS_ROOT . "/media-types") => $dir)
		{
			echo " $dir ";
		}
	}
	public function GetTypeFor()
	{

	}
}
class MediaType
{
	public $slug;
	public $formats;
	public $priority;

	public $type

	function __construct($directory)
	{
		$jsonFile = $directory . "/supports.json";			

		$conf = json_decode($jsonFile);

		$this->slug = $conf["slug"];
		$this->formats = $conf["supported-format"];
		$this->priority = $conf["priority"];
	}
}

?>
