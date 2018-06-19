<?php 
require_once SYS_ROOT . "/core/media.php";
require_once SYS_ROOT . "/core/mapping.php";
require_once SYS_ROOT . "/core/tag.php";
require_once SYS_ROOT . "/core/query.php";

require_once SYS_ROOT . "/core/user.php";
require_once SYS_ROOT . "/core/user-permission/role.php";
require_once SYS_ROOT . "/core/user-permission/permission.php";

require_once SYS_ROOT . "/utils/thumbnails.php";

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