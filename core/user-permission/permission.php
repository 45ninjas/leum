<?php

class Permission
{
	public $permission_id;
	public $slug;
	public $description;

	public static function CreateTable($dbc)
	{
		$sql = "CREATE table permissions
		(
			permission_id int unsigned auto_increment primary key,
			slug varchar(256) not null,
			description text
		)";

		$dbc->exec($sql);
	}

	public static function GetId($permission)
	{
		if($permission instanceof self)
			return $permission->permission_id;
		else if(is_numeric($permission))
			return $permission;
		
		throw new Exception("Bad permission index input");
	}

	// Getting one permission.
	public static function GetSingle($dbc, $permission)
	{
		if(is_string($permission))
			$sql = "SELECT permission_id, slug, description from permissions where slug = ?";
		else
		{
			$permission = self::GetID($permission);
			$sql = "SELECT permission_id, slug, description from permissions where permission_id = ?";
		}

		$statement = $dbc->prepare($sql);
		$statement->execute([$permission]);

		return $statement->fetchObject(__CLASS__);
	}
	// Getting multiple permissions.
	public static function GetMultiple($dbc, $permission_ids)
	{
		if(!is_array($permission_ids))
			die("Indexes is not an array.");

		$indexPlaceholder = LeumCore::PDOPlaceholder($permission_ids);
		$sql = "SELECT permission_id, slug, description from permissions where permission_id in ('$indexPlaceholder')";

		$statement = $dbc->prepare($sql);
		$statement->execute([$permission_ids]);

		return $statement->fetchAll(PDO::FETCH_CLASS, __CLASS__);
	}
	// Getting all permissions.
	public static function GetAll($dbc)
	{
		$sql = "SELECT permission_id, slug, description from permissions";

		$statement = $dbc->prepare($sql);
		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_CLASS, __CLASS__);
	}
	// Deleting single permissions.
	public static function DeleteSingle($dbc, $permission)
	{
		$permission = self::GetID($permission);

		$sql = "DELETE from permissions where permission_id = ?";
		$statement = $dbc->prepare($sql);
		$statement->execute([$permission]);

		return $statement->rowCount();
	}
	// Updating and inserting permissions.
	public static function InsertSingle($dbc, $permissionData, $index = null)
	{
		if($permissionData instanceof permission)
		{
			$permission = $permissionData;
			if(isset($permissionData->permission_id))
				$index = $mediaData->permission_id;
		}
		else
		{
			$permission = new permission();
			$permission->slug = $permissionData['slug'];
			$permission->description = $permissionData['description'];
		}
		if(is_numeric($index))
		{
			$sql = "UPDATE permissions SET slug = ?, description = ? WHERE permission_id = ?";

			$statement = $dbc->prepare($sql);
			$statement->execute([$permission->slug, $permission->description, $index]);
			return $index;
		}
		else
		{
			$sql = "INSERT INTO permissions (slug, description) VALUES (?, ?)";

			$statement = $dbc->prepare($sql);
			$statement->execute([$permission->slug, $permission->description]);
			return $dbc->lastInsertId();
		}
	}
}

?>