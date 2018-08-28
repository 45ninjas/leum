<?php

/**
 * Plugin Manager
 */
class PluginManager
{
	public $plugins = array();
	public function LoadPlugin($pluginName)
	{
		$pluginFile = SYS_ROOT . "/leum/plugins/$pluginName/$pluginName.php";
		if(is_file($pluginFile))
		{
			include_once $pluginFile;
			$pluginClass = new $pluginName;

			array_push($this->plugins, $pluginClass);

			Message::Create("info", "Loaded the '$pluginName' plug-in");
		}
	}
	public function __construct($pluginsToLoad)
	{

		foreach ($pluginsToLoad as $pluginName)
		{
			$this->LoadPlugin($pluginName);
		}
	}
}

Interface IPlugin
{
	function OnInit($core, $manager);
}

?>