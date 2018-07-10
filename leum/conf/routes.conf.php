<?php
// Routes for leum.
// Incorrect configuration of this file will result in pages not working.

// Routes change significantly during development. It's advised that you
// only change these if you really understand what's going on.
// Documentation and examples will be provided once leum exits early
// development.

// These settings are used throughout leum and must be valid. If not users will not be able to access the login pages.
// The login page file.
define('LOGIN_PAGE', "/user/login.php");
// The login route.
define('LOGIN_URL', "login");
// The register account page. If permissions allow 
define('REGISTER_PAGE', "/user/register.php");
define('HOME_PAGE', "/home.php");

// Destination page file.					Preg/Regex.					Group count (total arguments).
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
	["user/profile.php",					'profile',					0],

	// Import media.
	["edit/utilities/media_importer.php",		'edit/import',				0]
);
?>