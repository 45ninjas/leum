<?php

class Role
{
	public $role_id;
	public $name;

	public function GetPermissions($dbc)
	{
		RolePermissionMap::GetPermissions($dbc, $role_id);
	}

	public static function CreateTable($dbc)
	{
		$sql = "CREATE table roles
		(
			role_id int unsigned auto_increment primary key,
			slug varchar(256) not null,
			description text
		)";

		$dbc->exec($sql);
	}
	// Getting one role.
	public static function GetSingle($dbc, $role)
	{
		if(is_string($role))
			$sql = "SELECT role_id, slug, description from roles where slug = ?";
		else
		{
			$role = self::GetID($role);
			$sql = "SELECT role_id, slug, description from roles where role_id = ?";
		}

		$statement = $dbc->prepare($sql);
		$statement->execute([$role]);

		return $statement->fetchObject(__CLASS__);
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
	public static function GetAll($dbc)
	{
		$sql = "SELECT role_id, slug, description from roles";

		$statement = $dbc->prepare($sql);
		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_CLASS, __CLASS__);
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
		if($roleData instanceof role)
		{
			$role = $roleData;
			if(isset($roleData->role_id))
				$index = $mediaData->role_id;
		}
		else
		{
			$role = new role();
			$role->slug = $roleData['slug'];
			$role->description = $roleData['description'];
		}
		if(is_numeric($index))
		{
			$sql = "UPDATE roles SET slug = ?, description = ? WHERE role_id = ?";

			$statement = $dbc->prepare($sql);
			$statement->execute([$role->slug, $role->description, $index]);
			return $index;
		}
		else
		{
			$sql = "INSERT INTO roles (slug, description) VALUES (?, ?)";

			$statement = $dbc->prepare($sql);
			$statement->execute([$role->slug, $role->description]);
			return $dbc->lastInsertId();
		}
	}

	private static function GetID($role)
	{
		if($role instanceof role)
			return $role->role_id;
		else if(is_numeric($role))
			return $role;

		throw new Exception("Bad role index input");
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
			foreign key (role_id) references roles(role_id) on delete cascade,
			foreign key (permission_id) references permissions(permission_id) on delete cascade
		)
		";
		$dbc->exec($sql);
	}
	public static function GetPermissions($dbc, $role_id, $slugsOnly = false)
	{
		if($slugsOnly)
			$sql = "SELECT tags.slug from role_permission_map";
		else
			$sql = "SELECT * from role_permission_map";

		$sql .= " inner join roles on role_permission_map.role_id = roles.role_id
		inner join permissions on role_permission_map.permission_id = permissions.permission_id
		where roles.role_id = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$role_id]);

		if($slugsOnly)
			return $statement->fetchAll(PDO::FETCH_COLUMN);
		else
			return $statement->fetchAll(PDO::FETCH_CLASS, __CLASS__);
	}
	public static function SetPermission($dbc, $role_id, $permissions)
	{
		
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
}

?>