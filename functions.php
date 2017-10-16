<?php
require_once 'prefrences.php';

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

?>
