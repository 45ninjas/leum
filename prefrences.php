<?php
// === Site Configuration ===
// === Interface preferences ===
// The web root of the site.
define('ROOT', '/leum/');
// The title of the app.
define('APP_TITLE', 'Leum');

define('PAGE_SIZE', 20);

//	=== Database ===
define('DB_HOST', 'localhost');
define('DB_PASS', 'change me');
define('DB_USER', 'leum');
define('DB_NAME', 'leum');


// === Advanced stuff (no touch) ===
// The directory where the media is stored relative to this directory.
define('MEDIA_DIR', 'content/media/');
define('THUMB_DIR', 'content/thumbs/');

// === Routes ===
// Try not to touch theses as messing up any of these preg/regex strings could lead 
// to pages not working properly.
// 
// ONLY MODIFY IF YOU UNDERSTAND WHAT'S GOING ON IN THE 'dispatcher.php' FILE 
// 		Destination.					Preg/Regex.							Group count.
$routes = array(
	["home.php",			'',								0],
	["preferences.php",		'preferences',					0],
	["browse.php",			'browse',						0],
	["view.php",			'view\/(\d+)',					1],
	["media/list.php",		'edit\/media',					0],
	["media/edit.php",		'edit\/media\/(\d+)',			1],
	["media/delete.php",	'edit\/media\/delete\/(\d+)',	1],
	["media/edit.php",		'edit\/media\/new',				0],
);
?>