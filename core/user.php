<?php

class User
{
	public $user_id;
	public $usermame;
	public $last_login;
	public $email;

	public $permissions;
	public $roles;

	public function GetRoles($dbc)
	{
		// Get roles that are assigned to this user.
	}
	public function GetPermissions($dbc)
	{
		// Get permissions that are assigned to this user from all roles.
	}

	public static function CreateTable($dbc)
	{
		$sql = "CREATE table users
		(
			user_id int unsigned auto_increment primary key,
			username varchar(256) not null,
			last_login datetime not null,
			email text
		)";

		$dbc->exec($sql);
	}
	// Getting one user.
	public static function GetSingle($dbc, $user)
	{
		if(is_string($user))
			$sql = "SELECT user_id, username, last_login, email from users where username = ?";
		else
		{
			$user = self::GetID($user);
			$sql = "SELECT user_id, username, last_login, email from users where user_id = ?";
		}

		$statement = $dbc->prepare($sql);
		$statement->execute([$user]);

		return $statement->fetchObject(__CLASS__);
	}
	// Getting multiple users.
	public static function GetMultiple($dbc, $user_ids)
	{
		if(!is_array($user_ids))
			die("Indexes is not an array.");

		$indexPlaceholder = LeumCore::PDOPlaceholder($user_ids);
		$sql = "SELECT user_id, username, last_login, email from users where user_id in ('$indexPlaceholder')";

		$statement = $dbc->prepare($sql);
		$statement->execute([$user_ids]);

		return $statement->fetchAll(PDO::FETCH_CLASS, __CLASS__);
	}
	// Getting all users.
	public static function GetAll($dbc)
	{
		$sql = "SELECT user_id, username, last_login, email from users";

		$statement = $dbc->prepare($sql);
		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_CLASS, __CLASS__);
	}
	// Deleting single users.
	public static function DeleteSingle($dbc, $user)
	{
		$user = self::GetID($user);

		$sql = "DELETE from users where user_id = ?";
		$statement = $dbc->prepare($sql);
		$statement->execute([$user]);

		return $statement->rowCount();
	}
	// Updating and inserting users.
	public static function InsertSingle($dbc, $userData, $index = null)
	{
		if($userData instanceof User)
		{
			$user = $userData;
			if(isset($userData->user_id))
				$index = $mediaData->user_id;
		}
		else
		{
			$user = new User();
			$user->username = $userData['username'];
			$user->email = $userData['email'];
		}
		if(is_numeric($index))
		{
			$sql = "UPDATE users SET username = ?, email = ? WHERE user_id = ?";

			$statement = $dbc->prepare($sql);
			$statement->execute([$user->username, $user->email, $index]);
			return $index;
		}
		else
		{
			$sql = "INSERT INTO users (username, email) VALUES (?, ?)";

			$statement = $dbc->prepare($sql);
			$statement->execute([$user->username, $user->email]);
			return $dbc->lastInsertId();
		}
	}

	private static function GetID($user)
	{
		if($user instanceof User)
			return $user->user_id;
		else if(is_numeric($user))
			return $user;

		throw new Exception("Bad user index input");
	}
}

?>