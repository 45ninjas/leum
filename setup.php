<?php 
if(!defined('SYS_ROOT'))
	define('SYS_ROOT', __DIR__);
// Leum setup. Run this the first time you install leum.

require_once SYS_ROOT . "/functions.php";
require_once SYS_ROOT . "/core/leum-core.php";
require_once SYS_ROOT . "/core/leum-core.php";

$dbc = DBConnect();

header("Content-Type: text/plain");

if(!TableExists($dbc, "media"))
{
	echo "Creating the media table\n";
	Media::CreateTable($dbc);
}
else
	echo "media table already exists, skipping\n";

if(!TableExists($dbc, "tags"))
{
	echo "Creating the tags table\n";
	Tag::CreateTable($dbc);
}

else
	echo "tags table already exists, skipping\n";

if(!TableExists($dbc, "map"))
{
	echo "Creating the map table\n";
	Mapping::CreateTable($dbc);
}
else
	echo "map table already exists, skipping\n";

if(!TableExists($dbc, "task"))
{
	echo "Creating the task table\n";
	Task::CreateTable($dbc);
}
else
	echo "task table already exists, skipping\n";

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