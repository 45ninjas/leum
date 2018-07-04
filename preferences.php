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

// === Passwords ===
// http://php.net/manual/en/function.password-hash.php#example-985
define('AUTH_PASS_COST', 10);

// === Roles ===
// Do not change unless you have modified the core/user-permission/defaults.php file BEFORE
// setting up leum or you know what these actually do behind the scenes.
// The default user role, used for unauthenticated users.
define('DEFAULT_ROLE','default');
// The role a new user is assigned when they register an account or an admin creates an account.
define('NEW_USER_ROLE','user');

// === Routes ===
// These settings are used throughout leum and must be valid. If not users will not be able to access the login pages.
// The login page file.
define('LOGIN_PAGE', "/user/login.php");
// The login route.
define('LOGIN_URL', "login");
// The register account page. If permissions allow 
define('REGISTER_PAGE', "/user/register.php");
define('HOME_PAGE', "/home.php");
// 
// 
// Try not to touch theses as messing up any of these preg/regex strings and group counts will lead 
// to some or all pages not working.
// 
// 
// ONLY MODIFY IF YOU UNDERSTAND WHAT'S GOING ON IN THE 'dispatcher.php' FILE 
// 
//	Destination page.			Preg/Regex.						Group count (total arguments).
$routes = array(
	[HOME_PAGE,								'',							0],
	["preferences.php",						'preferences',				0],
	["browse.php",							'browse',					0],
	["view.php",							'view/(\d+)',				1],
	["edit/landing.php",					'edit',						0],
	
	// Media Editing
	["edit/media/edit_media.php",			'edit/media\/(\d+)',		1],
	["edit/media/edit_media.php",			'edit/media\/new',			0],
	["edit/media/list_media.php",			'edit/media',				0],
	
	// Tag Editing
	["edit/tags/edit_tags.php",				'edit/tag/(\d+)',			1],
	["edit/tags/edit_tags.php",				'edit/tag/new',				0],
	["edit/tags/list_tags.php",				'edit/tag',					0],

	// User Editing.
	["edit/users/edit_user.php",			'edit/user/(\d+)',			1],
	["edit/users/edit_user.php",			'edit/user/new',			0],
	["edit/users/list_users.php",			'edit/user',				0],

	// Permissions and roles.
	["edit/permissions/edit_permissions.php",	'edit/permissions',						0],
	["edit/permissions/edit_roles.php",			'edit/permissions/roles/new',			0],
	["edit/permissions/edit_roles.php",			'edit/permissions/roles/([-a-z1-9]+)',	1],

	// User pages.
	[REGISTER_PAGE,							'register',					0],
	[LOGIN_PAGE,							LOGIN_URL,					0],
	[LOGIN_PAGE,							LOGIN_URL . '/(forgot)',	1],
	["user/profile.php",					'profile',					0]
);
?>