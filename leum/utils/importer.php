<?php
if(!defined('SYS_ROOT'))
	define('SYS_ROOT', realpath(__DIR__ . "/.."));
require_once SYS_ROOT . "/core/leum-core.php";

header("Content-Type: text/plain");

$dbc = DBConnect();

$path = SYS_ROOT . MEDIA_DIR;

if(isset($_GET['directory']))
{
	$path = $_GET['directory'];
}

$path = rtrim($path, '/') . '/';

$directory = new RecursiveDirectoryIterator($path);
$iterator = new RecursiveIteratorIterator($directory);

foreach($iterator as $info)
{
	if(!$info->isFile())
		continue;
	
	$localPath = substr($info->getPathname(), strlen($path));

	$items = Media::GetFromPath($dbc, $localPath);
	//var_dump($items);
	if(count($items) == 0)
	{
		$data = array();
		$data['title'] = $info->getFilename();
		$data['source'] = "Leum automatic media importer.";
		$data['path'] = $localPath;
		Media::InsertSingle($dbc, $data);
		echo "Creating $localPath\n";
	}
	else
	{
		echo "Skipping $localPath\n";
	}
}
echo "Done\n";
?>