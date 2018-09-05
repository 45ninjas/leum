<?php

// The default user role, used for unauthenticated users.
define('DEFAULT_ROLE','default');
// The role a new user is assigned when they register an account or an admin creates an account.
define('NEW_USER_ROLE','user');

// === Settings below are only applied when leum is being set-up ===
// At the moment, changing these after setup have no affect.
function GetPermissions()
{
	return array
	(
		// Essentials
		'access-app' 				=> "Can access the app.",
		'login'						=> "Allowed to login.",
		'edit-account'				=> "Edit personal profile.",
		'reset-password'			=> "Reset password.",
		'register-account'			=> "Register account.",
		'admin-pages'				=> "Allowed to see the admin pages.",
		// Media
		'media-create'				=> "Create new media items.",
		'media-edit'				=> "Edit existing media items.",
		'media-delete'				=> "Delete existing media items.",
		'media-tags'				=> "Remove and Add tags to media items.",
		'media-upload'				=> "Upload new media items. (Should have 'media-create')",
		// Tags
		'tags-create'				=> "Create new tags",
		'tags-edit'					=> "Edit existing tags",
		'tags-delete'				=> "Delete existing tags",
		// Users
		'users-create'				=> "Create new users",
		'users-edit'				=> "Edit existing users",
		'users-delete'				=> "Delete existing users",
		'users-reset'				=> "Reset user passwords",
		// Permissions
		'permissions-edit'			=> "Edit, assign and view roles and permissions"
	);
}
function GetRoles()
{
	return array
	(
		"root" => [
			"Root access, Has all permissions, system level. Only used to edit permissions.",
			array_keys(GetPermissions())
		],
		"default" => [
			"Default user privileges for all users including un-authenticated users.",
			[
				'access-app',
				'register-account',
			]
		],
		"user" => [
			"General user privileges like editing account, being able to login, allowed to access the app ect.",
			[
				'login',
				'edit-account',
				'reset-password'
			]
		],
		"moderator" => [
			"Moderator privileges like editing media and tags.",
			[
				'media-create',
				'media-edit',
				'media-delete',
				'media-tags',
				'media-upload',
				'tags-create',
				'tags-edit',
				'tags-delete'
			]
		],
		"administrator" => [
			"Administrator privileges like editing users.",
			[
				'users-create',
				'users-edit',
				'users-delete',
				'users-reset',
				'admin-pages'
			]
		],
		"suspended" => [
			"Suspended users don't get to have any",
				[
				]
	]);
}

?>
