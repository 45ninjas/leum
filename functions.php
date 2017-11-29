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
	
	$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME .";charset=utf8", DB_USER, DB_PASS, $opt);
	return $pdo;
}

function Asset($asset)
{
	echo GetAsset($asset);
}
function GetAsset($asset)
{
	return ROOT.$asset;
}

function TheTitle()
{
	echo Leum::Instance()->title;
}
function TheContent()
{
	Leum::Instance()->Output();
}
function TheHead()
{
	Leum::Instance()->Head();
}
?>
