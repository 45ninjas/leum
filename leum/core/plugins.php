<?php

/**
 * Plugin Manager
 */
class PluginManager
{
	public $plugins = array();

	// Load a plugin from the plugins directory.
	public function LoadPlugin($pluginName)
	{
		// Check to see if the plug-in exists.
		$pluginFile = SYS_ROOT . "/leum/plugins/$pluginName/$pluginName.php";
		if(is_file($pluginFile))
		{
			include_once $pluginFile;
			$pluginClass = new $pluginName();

			// Add this new plugin to the array of plugins
			array_push($this->plugins, $pluginClass);
		}
		else
		{
			Log::Write("Plug-in '$pluginName' does not exist in '$pluginFile'");
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

class Plugin
{
	// The Plugin class does not currently do anything.
}

?>