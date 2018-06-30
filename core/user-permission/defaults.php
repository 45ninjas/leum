<?php

require_once SYS_ROOT . "/core/leum-core.php";

function SetupDefaults($dbc)
{
	$permissions = array
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

	$roles = array
	(
		"root" => [
			"Root access, Has all permissions, system level. Only used to edit permissions.",
			array_keys($permissions)
		],
		"default" => [
			"Default user privileges for all users including un-authenticated users.",
			[
				'access-app',
				'register-account',
				'reset-password'
			]
		],
		"user" => [
			"General user privileges like editing account, being able to login, allowed to access the app ect.",
			[
				'login',
				'edit-account'
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

	try
	{
		// Add all the permissions.
		foreach ($permissions as $slug => $description)
		{
			Permission::InsertSingle($dbc, ["slug" => $slug, "description" => $description]);
		}
		echo "Added default permissions\n";

		// Add all the roles.
		foreach ($roles as $role => $slugs)
		{
			$title = array_shift($slugs);
			$role_id = Role::InsertSingle($dbc, ["slug" => $role, "description" => $title]);
			RolePermissionMap::SetPermissions($dbc, $role_id, $slugs[0]);

			echo "Added $role role\n";
		}	
	}
	catch (Exception $e)
	{
		throw $e;
	}
}
?>