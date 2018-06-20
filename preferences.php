<?php
// === Site Configuration ===
// 
// === Interface preferences ===
// 
// The web root of the site. (no trailing slash)
define('ROOT', '/leum');
// The title of the app.
define('APP_TITLE', 'Leum');

define('PAGE_SIZE', 100);

//	=== Database ===
//	
define('DB_HOST', 'localhost');
define('DB_PASS', 'change me');
define('DB_USER', 'leum');
define('DB_NAME', 'leum');


// === Advanced stuff (no touch) ===
// 
// The directory where the media is stored relative to this directory. (no trailing slash)
define('MEDIA_DIR', '/content/media');
define('THUMB_DIR', '/content/thumbs');
define('THUMB_SIZE', 196);
define('LOG_DIR', SYS_ROOT . '/logs');
define('API_URL', ROOT . "/api");

// The document title prefix and suffix(the name of the tab on the browser.)
// Don't forget to add spaces where needed.
// === Examples ===
// 		prefix: APP_TITLE + " - "
// 		suffix: ""
// 		result "Leum - Edit Media" (assuming APP_TITLE is 'Leum').
// 	Another one!
// 		prefix: ""
// 		suffix: " CoolPics"
// 		result: "Edit Media CoolPics"
define('TITLE_PREFIX', "Leum - ");
define('TITLE_SUFFIX', "");

// === Routes ===
// 
// Try not to touch theses as messing up any of these preg/regex strings and group counts will lead 
// to some or all pages not working.
// 
// 
// ONLY MODIFY IF YOU UNDERSTAND WHAT'S GOING ON IN THE 'dispatcher.php' FILE 
// 
//	Destination page.			Preg/Regex.						Group count (total arguments).
$routes = array(
	["home.php",							'',							0],
	["preferences.php",						'preferences',				0],
	["browse.php",							'browse',					0],
	["view.php",							'view\/(\d+)',				1],
	["edit/landing.php",					'edit',						0],
	
	// Media Editing
	["edit/media/edit.php",					'edit\/media\/(\d+)',		1],
	["edit/media/edit.php",					'edit\/media\/new',			0],
	["edit/media/list.php",					'edit\/media',				0],
	
	// Tag Editing
	["edit/tags/edit.php",					'edit\/tag\/(\d+)',			1],
	["edit/tags/edit.php",					'edit\/tag\/new',			0],
	["edit/tags/list.php",					'edit\/tag',				0],

	// User Editing.
	["edit/users/edit-user.php",			'edit\/user\/(\d+)',		1],
	["edit/users/edit-user.php",			'edit\/user\/new',			0],
	["edit/users/list-users.php",			'edit\/user',				0],
	// Permissions.
	["edit/permissions/edit-permissions.php", 'edit\/permissions',		0],
);
?>