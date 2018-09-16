<?php
// Routes for leum.
// Incorrect configuration of this file will result in pages not working.

// Routes change significantly during development. It's advised that you
// only change these if you really understand what's going on.
// Documentation and examples will be provided once leum exits early
// development.

// These settings are used throughout leum and must be valid. If not users will not be able to access the login pages.
// The login page file.
define('LOGIN_PAGE', "pages/user/login.php");
// The login route.
define('LOGIN_URL', "login");
// The register account page. If permissions allow 
define('REGISTER_PAGE', "pages/user/register.php");
define('HOME_PAGE', "pages/home.php");

function FrontRoutes()
{
	Dispatcher::AddRoute('',					HOME_PAGE);
	Dispatcher::AddRoute('preferences',			'pages/preferences.php');
	Dispatcher::AddRoute('browse',				'pages/browse.php');
	Dispatcher::AddRoute('view',				'pages/view.php');
	Dispatcher::AddRoute('view/%index%',		'pages/view.php');
	Dispatcher::AddRoute('edit',				'pages/edit/landing.php');

	// Media Editing
	Dispatcher::AddRoute('edit/media/%index%',	'pages/edit/media/edit_media.php');
	Dispatcher::AddRoute('edit/media/new',		'pages/edit/media/edit_media.php');
	Dispatcher::AddRoute('edit/media',			'pages/edit/media/list_media.php');

	// Tag Editing
	Dispatcher::AddRoute('edit/tag/%index%',	'pages/edit/tags/edit_tags.php');
	Dispatcher::AddRoute('edit/tag/new',		'pages/edit/tags/edit_tags.php');
	Dispatcher::AddRoute('edit/tag',			'pages/edit/tags/list_tags.php');

	// User Editing
	Dispatcher::AddRoute('edit/user/%index%',	'pages/edit/users/edit_user.php');
	Dispatcher::AddRoute('edit/user/new',		'pages/edit/users/edit_user.php');
	Dispatcher::AddRoute('edit/user',			'pages/edit/users/list_users.php');

	// Permissions and roles
	Dispatcher::AddRoute('edit/permissions/roles/%slug%',	'pages/edit/permissions/edit_roles.php');
	Dispatcher::AddRoute('edit/permissions/roles/new',		'pages/edit/permissions/edit_roles.php');
	Dispatcher::AddRoute('edit/permissions',				'pages/edit/permissions/edit_permissions.php');

	Dispatcher::AddRoute('register',			REGISTER_PAGE);
	Dispatcher::AddRoute(LOGIN_URL,				LOGIN_PAGE);
	Dispatcher::AddRoute('profile',				'pages/user/profile.php');

	// Import utility
	Dispatcher::AddRoute('edit/import',			'pages/edit/utilities/media_importer.php');
}
?>