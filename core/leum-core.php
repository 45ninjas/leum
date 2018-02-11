<?php 
require_once SYS_ROOT . "/core/media.php";
require_once SYS_ROOT . "/core/mapping.php";
require_once SYS_ROOT . "/core/tag.php";
require_once SYS_ROOT . "/core/query.php";

class LeumCore
{
	public static function PDOPlaceholder($array)
	{
		return str_repeat('?, ', count($array) - 1) . '?';
	}
	public static function GetTotalItems($dbc)
	{
		return $dbc->query("SELECT found_rows()")->fetch()["found_rows()"];
	}
}
 ?>