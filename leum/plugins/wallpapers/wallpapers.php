<?php
/*
Author: Those45Ninjas
Title: wallpapers

This plugin adds the ability to manage wallpapers.
*/


class Wallpapers extends Plugin
{
	public static $instance;
	public static $wallpaper;
	public function __construct()
	{
		LeumCore::AddHook("leum.front.routes", [$this, 'AssignRoutes']);
		// LeumCore::AddHook("leum.setup", [$this, 'Setup']);

		self::$instance = $this;
	}

	public function AssignRoutes()
	{
		Dispatcher::AddRoute("edit/wallpapers", 'plugins/wallpapers/edit.php');
	}

	public function GetAllWallpapers($dbc)
	{
		$sql = "SELECT file, title, id from media where type = 'wallpaper';";
		$statement = $dbc->query($sql);

		$results = [];

		while ($row = $statement->fetch())
		{
			$row['link'] = ROOT . MEDIA_DIR . "/" . $row['file'];
			$results[] = $row;
		}
		return $results;
	}
	public function GetSingleWallpaper($dbc, $id = null)
	{
		if($id == null)
		{
			$sql = "SELECT file, id from media where type = 'wallpaper' order by RAND() limit 1;";
			$statement = $dbc->query($sql);
			return $statement->fetch();
		}
		else
		{
			$sql = "SELECT file, id from media where type = 'wallpaper' and id = ?";

			$statement = $dbc->prepare($sql);
			$statement->execute([$id]);

			return $statement->fetch();
		}
	}

	public static function GetWallpaper()
	{
		if(!isset(self::$wallpaper))
		{
			self::$wallpaper = self::$instance->GetSingleWallpaper(LeumCore::$dbc);
			if(self::$wallpaper != false)
				self::$wallpaper['link'] = ROOT . MEDIA_DIR . "/" . self::$wallpaper['file'];
		}
		return self::$wallpaper;
	}
	public static function BackgroundStyle()
	{
		$wallpaper = self::GetWallpaper();

		if($wallpaper == false)
			return;

		$link = $wallpaper['link'];

		echo "style=\"background-image: url($link)\"";
	}
}

?>