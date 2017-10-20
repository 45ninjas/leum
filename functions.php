<?php
require_once 'prefrences.php';
require_once 'application/libs/view.interface.php';

function DBConnect()
{
	global $prefrences;
	$opt = [
		PDO::ATTR_ERRMODE				=> PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE	=> PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES		=> false,
	];
	
	$pdo = new PDO("mysql:host=" . $prefrences["db host"] . ";dbname=" . $prefrences["db name"] .";charset=utf8", $prefrences["db user"], $prefrences["db password"], $opt);
	return $pdo;
}

function asset($asset)
{
	global $prefrences;
	echo $prefrences['root'].$asset;
}

function InterfaceRouter($request)
{
	$parts = explode('/', $request);
	$parts = array_filter($parts);

	// if there is noting show the home-page.
	if(count($parts) == 0)
		return SetupView("pages/home.php");

	$first = strtolower(array_shift($parts));
	$second = strtolower(array_shift($parts));

	// Looks like the view is in the views/
	if(is_file("application/views/$first/$second.php"))
	{
		return SetupView("application/views/$first/$second.php");
	}
	// I give up, can't find it. 404.
	return SetupView("pages/404.php");
}
function SetupView($viewPath, $arguments = null)
{
	include $viewPath;
	$view = new Viewable($arguments);
	return $view;
}
?>
