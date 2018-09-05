<?php

class Role
{
	public $role_id;
	public $slug;
	public $description;
	public $permissions;

	public function AddPermission($dbc, $permission)
	{
		RolePermissionMap::Map($dbc, $permission, $this->role_id);	
	}
	public function RemovePermission($dbc, $permission)
	{
		RolePermissionMap::Unmap($dbc, $permission, $this->role_id);
	}
	public function GetPermissions($dbc)
	{
		$this->permissions = RolePermissionMap::GetAllMapped($dbc, $this->role_id, true);
	}

	public function HasPermission($permission)
	{
		if(!isset($this->permissions) || !is_array($this->permissions))
		{
			throw new Exception("Role's permissions are not set. Use 'GetPermissions()'");
		}
		return in_array($permission, $this->permissions);
	}
	public function HasPermissions($permissions)
	{
		if(!isset($this->permissions) || !is_array($this->permissions))
		{
			throw new Exception("Role's permissions are not set. Use 'GetPermissions()'");
		}

		return !array_diff($permissions, $this->permissions);
	}

	public static function GetId($role, $dbc = null)
	{
		if($role instanceof self)
			return $role->role_id;
		else if(is_numeric($role))
			return $role;
		else if($dbc != null && is_string($role))
		{
			$statement = $dbc->prepare("SELECT role_id from roles where slug = ?");
			$statement->execute([$role]);

			$result = $statement->fetchColumn();
			if($result != false && is_numeric($result))
				return $result;
		}
		throw new Exception("Bad role index input");
	}
	public static function CreateTable($dbc)
	{
		$sql = "CREATE table roles
		(
			role_id int unsigned auto_increment primary key,
			slug varchar(32) not null unique key,
			description text
		)";

		$dbc->exec($sql);
	}
	// Getting one role.
	public static function GetSingle($dbc, $role, $withPermissions = false)
	{
		if(is_string($role))
			$field = "slug";
		else
		{
			$field = "role_id";
			$role = self::GetID($role);
		}

		if($withPermissions)
			$sql = "SELECT role_id, slug, description,
			(SELECT DISTINCT GROUP_CONCAT(p.slug)
				FROM permissions p
				JOIN role_permission_map rpm ON p.permission_id = rpm.permission_id
				where rpm.role_id = r.role_id) as permissions
			from roles as r where $field = ?;";
		else
			$sql = "SELECT role_id, slug, description from roles where $field = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$role]);

		$return = $statement->fetchObject(__CLASS__);

		if($withPermissions)
			$return->permissions = explode(',', $return->permissions);

		return $return;
	}
	// Getting multiple roles.
	public static function GetMultiple($dbc, $role_ids)
	{
		if(!is_array($role_ids))
			die("Indexes is not an array.");

		$indexPlaceholder = LeumCore::PDOPlaceholder($role_ids);
		$sql = "SELECT role_id, slug, description from roles where role_id in ('$indexPlaceholder')";

		$statement = $dbc->prepare($sql);
		$statement->execute([$role_ids]);

		return $statement->fetchAll(PDO::FETCH_CLASS, __CLASS__);
	}
	// Getting all roles.
	public static function GetAll($dbc, $withPermissions)
	{
		if($withPermissions)
			$sql = "SELECT role_id, slug, description,
			(SELECT DISTINCT GROUP_CONCAT(p.slug)
				FROM permissions p
				JOIN role_permission_map rpm ON p.permission_id = rpm.permission_id
				where rpm.role_id = r.role_id) as permissions
			from roles as r;";
		else
			$sql = "SELECT role_id, slug, description from roles;";

		$statement = $dbc->query($sql);
		//$statement->query();

		$roles = $statement->fetchAll(PDO::FETCH_CLASS, __CLASS__);

		if($withPermissions)
		{
			foreach ($roles as $role)
			{
				$role->permissions = explode(',', $role->permissions);	
			}
		}
		return $roles;
	}
	// Deleting single roles.
	public static function DeleteSingle($dbc, $role)
	{
		$role = self::GetID($role);

		$sql = "DELETE from roles where role_id = ?";
		$statement = $dbc->prepare($sql);
		$statement->execute([$role]);

		return $statement->rowCount();
	}
	// Updating and inserting roles.
	public static function InsertSingle($dbc, $roleData, $index = null)
	{
		// Get the index from the role if it's a 'role'
		if($roleData instanceof role)
		{
			$role = $roleData;
			if(isset($roleData->role_id))
				$index = $roleData->role_id;
		}
		// Make a role from a std array.
		else
		{
			$role = new role();
			$role->slug = $roleData['slug'];
			$role->description = $roleData['description'];
		}

		// Clean the slug.
		$role->slug = LeumCore::CreateSlug($role->slug);

		// Update an existing role.
		if(is_numeric($index))
		{
			$sql = "UPDATE roles SET slug = ?, description = ? WHERE role_id = ?";

			$statement = $dbc->prepare($sql);
			$statement->execute([$role->slug, $role->description, $index]);
			return $index;
		}

		// Or create a new role.
		else
		{
			$sql = "INSERT INTO roles (slug, description) VALUES (?, ?)";

			$statement = $dbc->prepare($sql);
			$statement->execute([$role->slug, $role->description]);
			return $dbc->lastInsertId();
		}
	}
}

class RolePermissionMap
{
	public $map_id;
	public $role_id;
	public $permission_id;

	public static function CreateTable($dbc)
	{
		$sql = "CREATE table role_permission_map
		(
			map_id int unsigned auto_increment primary key,
			role_id int unsigned not null,
			permission_id int unsigned not null,
			unique key (role_id, permission_id),
			foreign key (role_id) references roles(role_id) on delete cascade,
			foreign key (permission_id) references permissions(permission_id) on delete cascade
		)
		";
		$dbc->exec($sql);
	}

	public static function GetDefaultPermissions($dbc)
	{
		return self::GetAllMapped($dbc, DEFAULT_ROLE, true);	
	}

	//RolePermissionMap::Map($dbc, $permission, $role_id);
	public static function Map($dbc, $role, $permission)
	{
		$role = Role::GetId($role);
		$permission = Permission::GetId($permission);

		$sql = "INSERT into role_permission_map (role_id, permission_id) values (?, ?)";

		$statement = $dbc->prepare($sql);
		$statement->execute([$role, $permission]);
	}
	//RolePermissionMap::Unmap($dbc, $permission, $role_id);
	public static function Unmap($dbc, $role, $permission)
	{
		$role = Role::GetId($role);
		$permission = Permission::GetId($permission);

		$sql = "DELETE from role_permission_map where role_id = ? and  permission_id = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$role, $permission]);
	}
	//RolePermissionMap::GetAllMapped($dbc, $role_id);
	public static function GetAllMapped($dbc, $role, $slugsOnly = false)
	{
		if(is_string($role))
			$field = "slug";
		else
		{
			$role = Role::GetId($role);
			$field = "role_id";
		}

		// Set the select variable to the correct value based on the slug flag.
		if($slugsOnly)
			$select = "permissions.slug";
		else
			$select = "permissions.*";

		if($role)

		// Run the sql.
		$sql = "SELECT $select from role_permission_map
		inner join roles on role_permission_map.role_id = roles.role_id
		inner join permissions on role_permission_map.permission_id = permissions.permission_id
		where roles.$field = ?";
		$statement = $dbc->prepare($sql);
		$statement->execute([$role]);

		// Return the results, if slugs only the return an array of slugs.
		if($slugsOnly)
			return $statement->fetchAll(PDO::FETCH_COLUMN);
		else
			return $statement->fetchAll(PDO::FETCH_CLASS, 'Permission');
	}
	public static function UnmapAll($dbc, $role)
	{
		$role = Role::GetId($role);

		$sql = "DELETE from role_permission_map where role_id = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$role]);

		return $statement->rowCount();
	}
	// Sets the permissions.
	public static function SetPermissions($dbc, $role, $newPermissions)
	{
		$role = Role::GetId($role);

		// If no permissions have been set than remove all permissions from this role.
		if(is_null($newPermissions) || count($newPermissions) == 0)
		{
			self::UnmapAll($dbc, $role);
			return;
		}

		// Get the id's of the new permissions.
		$indexPlaceholder = LeumCore::PDOPlaceholder($newPermissions);
		$sql = "SELECT permission_id from permissions where slug in ($indexPlaceholder)";
		$statement = $dbc->prepare($sql);
		$statement->execute($newPermissions);
		$newPermissionIds = $statement->fetchAll(PDO::FETCH_COLUMN);

		// Get the ids of each permission already mapped to the role.
		$sql = "SELECT permissions.permission_id from role_permission_map
		inner join permissions on role_permission_map.permission_id = permissions.permission_id
		where role_id = ?";
		$statement = $dbc->prepare($sql);
		$statement->execute([$role]);
		$permissionIds = $statement->fetchAll(PDO::FETCH_COLUMN);

		// Figure out what id's need to be removed, added and not touched.
		$permissionsToRemove = array();
		$permissionsToAdd = $newPermissionIds;

		foreach($permissionIds as $dbPerm)
		{
			if(!in_array($dbPerm, $newPermissionIds))
				$permissionsToRemove[] = $dbPerm;

			unset($permissionsToAdd[array_search($dbPerm, $newPermissionIds)]);
		}
		// Apply the changes needed to be made.
		if($dbc->beginTransaction())
		{
			try
			{
				// Remove all the permissions to remove.
				while($permission = array_pop($permissionsToRemove))
					self::Unmap($dbc, $role, $permission);

				// Add all the permissions to add.
				while($permission = array_pop($permissionsToAdd))
					self::Map($dbc, $role, $permission);

				$dbc->commit();
			}
			catch (Exception $e)
			{
				if($dbc->inTransaction())
					$dbc->rollBack();

				throw $e;
			}
		}
		else
			throw new Exception("Unable to being a transaction");
	}
}
class UserRoleMap
{
	public $map_id;
	public $user_id;
	public $role_id;

	public static function CreateTable($dbc)
	{
		$sql = "CREATE table user_role_map
		(
			map_id int unsigned auto_increment primary key,
			user_id int unsigned not null,
			role_id int unsigned not null,
			foreign key (role_id) references roles(role_id) on delete cascade,
			foreign key (user_id) references users(user_id) on delete cascade
		)
		";
		$dbc->exec($sql);
	}
	//UserRoleMap::Map($dbc, $user, $role);
	public static function Map($dbc, $user, $role)
	{
		$role = Role::GetId($role);
		$user = User::GetId($user);

		$sql = "INSERT into user_role_map (role_id, user_id) values (?, ?)";

		$statement = $dbc->prepare($sql);
		$statement->execute([$role, $user]);
	}
	//UserRoleMap::Unmap($dbc, $user, $role);
	public static function Unmap($dbc, $user, $role)
	{
		$role = Role::GetId($role);
		$user = User::GetId($user);

		$sql = "DELETE from user_role_map where role_id = ? and  user_id = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$role, $user]);
	}
	//UserRoleMap::GetAllMapped($dbc, $user);
	public static function GetAllMapped($dbc, $user)
	{
		$user = Role::GetId($user);

		// Run the sql.
		$sql = "SELECT roles.* from user_role_map
		inner join users on user_role_map.user_id = users.user_id
		inner join roles on user_role_map.role_id = roles.role_id
		where users.user_id = ?";
		$statement = $dbc->prepare($sql);
		$statement->execute([$user]);

		return $statement->fetchAll(PDO::FETCH_CLASS, 'Role');
	}
}

?>