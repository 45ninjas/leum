<?php
/*
Author: Those45Ninjas
Title: wallpapers

This plugin adds the ability to manage wallpapers.
*/


class Importer extends Plugin
{
	public static $instance;
	public function __construct()
	{
		LeumCore::AddHook("leum.front.routes", [$this, 'AssignRoutes']);
		// LeumCore::AddHook("leum.setup", [$this, 'Setup']);

		self::$instance = $this;
	}

	public function AssignRoutes()
	{
		Dispatcher::AddRoute("edit/import", 'plugins/importer/pages/import.php');
	}
}

?>