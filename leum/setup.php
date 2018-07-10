<?php 
if(!defined('SYS_ROOT'))
	define('SYS_ROOT', __DIR__);
// Leum setup. Run this the first time you install leum.

require_once SYS_ROOT . "/functions.php";
require_once SYS_ROOT . "/core/leum-core.php";
require_once SYS_ROOT . "/core/leum-core.php";
require_once SYS_ROOT . "/core/user-permission/defaults.php";

$dbc = DBConnect();

header("Content-Type: text/plain");

// Core functionality tables.
CreateTable($dbc, "media",		'Media',		'CreateTable');
CreateTable($dbc, "tags",		'Tag',			'CreateTable');
CreateTable($dbc, "map",		'Map',			'CreateTable');

// Users and Roles.
CreateTable($dbc, "users", 'User', 'CreateTable');
CreateTable($dbc, "roles", 'Role', 'CreateTable');
CreateTable($dbc, "permissions",'Permission',	'CreateTable');
CreateTable($dbc, "role_permission_map", 'RolePermissionMap', 'CreateTable');
CreateTable($dbc, "user_role_map", 'UserRoleMap', 'CreateTable');

// Default Roles.
SetupDefaults($dbc);

function CreateTable($dbc, $tableName, $class, $method)
{
	if(TableExists($dbc, $tableName))
	{
		echo "$tableName already exists, skipping\n";
		return;
	}

	$class::$method($dbc);
	echo "$tableName was created\n";
}

function TableExists($dbc, $tableName)
{
	$sql = "SELECT * from information_schema.tables
	where table_schema = ? and table_name = ?
	limit 1";

	$statement = $dbc->prepare($sql);
	$statement->execute([DB_NAME, $tableName]);

	return $statement->fetch() != false;
}

?>