<?php
/*
Author: Those45Ninjas
Title: wallpapers

This plugin adds the ability to manage wallpapers.
*/


class Importer extends Plugin
{
	const VERSION = 1;
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
		Dispatcher::AddRoute("edit/export", 'plugins/importer/pages/import_export.php');
	}

	public static function HeaderMenu()
	{
		$menu = ['items' => [
			['href'=> ROOT . '/edit',			'content' => 'Edit'],
			['href'=> ROOT . '/edit/import',	'content' => 'Import Directory'],
			['href'=> ROOT . '/edit/export',	'content' => 'Import and Export utility']
		]];
		return Front::GetWidget('menu',$menu);
	}
}

?>