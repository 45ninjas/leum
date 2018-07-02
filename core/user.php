<?php

class User
{
	public $user_id;
	public $username;
	public $last_login;
	public $email;
	public $hash;

	public $permissions;
	public $roles;

	// Figures out if the user has a permission
	public function HasPermissions($permissions)
	{
		if(!isset($this->permissions) || !is_array($this->permissions))
			throw new Exception("User's permissions are not set. Use 'GetPermissions()'");

		return !array_diff($permissions, $this->permissions);
	}

	// Gets all permission slugs mapped to the user.
	public function GetPermissions($dbc)
	{
		$sql = "SELECT DISTINCT p.slug
		FROM permissions p
        JOIN role_permission_map rpm ON p.permission_id = rpm.permission_id
        JOIN roles ON rpm.role_id = roles.role_id
        JOIN user_role_map upm on rpm.role_id = upm.role_id
        JOIN users ON upm.user_id = users.user_id
        WHERE users.user_id = ?;";

        $statement = $dbc->prepare($sql);
        $statement->execute([$this->user_id]);

        $this->premissions = $statement->fetchAll(PDO::FETCH_COLUMN);
        return $this->permissions;
	}
	public function SetPassword($password, $dbc = null)
	{
		$this->hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => AUTH_PASS_COST]);

		if(isset($dbc))
		{
			$sql = "UPDATE users set hash = ? where user_id = ?";
			$statement = $dbc->prepare($sql);
			$statement->execute([$this->hash, $this->user_id]);
		}
	}
	public function Login($dbc)
	{
		$statement = $dbc->prepare("UPDATE users set last_login = NOW() where user_id = ?");
		$statement->execute([$this->user_id]);
	}
	public static function CreateTable($dbc)
	{
		$sql = "CREATE table users
		(
			user_id int unsigned auto_increment primary key,
			username varchar(255) not null,
			hash varchar(255) not null,
			last_login datetime not null,
			email text
		)";

		$dbc->exec($sql);
	}
	// Getting one user.
	public static function GetSingle($dbc, $user, $withPermissions = false, $everything = false)
	{
		if($everything)
			$columns = "*";
		else
			$columns = "users.user_id, users.username, users.last_login";

		if(!$withPermissions)
		{
			if(is_string($user))
				$sql = "SELECT $columns from users where username = ?";
			else
			{
				$user = self::GetID($user);
				$sql = "SELECT * from users where user_id = ?";
			}

			$statement = $dbc->prepare($sql);
			$statement->execute([$user]);

			return $statement->fetchObject(__CLASS__);
		}
		else
		{
			$sql = "SELECT $columns, (SELECT DISTINCT GROUP_CONCAT(p.slug)
			FROM permissions p
            JOIN role_permission_map rpm ON p.permission_id = rpm.permission_id
            JOIN roles ON rpm.role_id = roles.role_id
            JOIN user_role_map upm on rpm.role_id = upm.role_id
            JOIN users ON upm.user_id = users.user_id
            WHERE users.user_id = ?) AS permissions
            FROM users
            where users.user_id = ?;";

            $statement = $dbc->prepare($sql);
            $statement->execute([$user,$user]);

            $result = $statement->fetchObject(__CLASS__);
            $result->permissions = explode(',', $result->permissions);
			return $result;
		}
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
			$user->SetPassword($userData['password']);
		}
		if(is_numeric($index))
		{
			$sql = "UPDATE users SET username = ?, email = ?, hash = ? WHERE user_id = ?";

			$statement = $dbc->prepare($sql);
			$statement->execute([$user->username, $user->email, $user->hash, $index]);
			return $index;
		}
		else
		{
			$sql = "INSERT INTO users (username, email, hash) VALUES (?, ?, ?)";

			$statement = $dbc->prepare($sql);
			$statement->execute([$user->username, $user->email, $user->hash]);
			return (int)$dbc->lastInsertId();
		}
	}

	public static function CheckPassword($dbc, $user, $password)
	{
		if(is_string($user))
			$sql = "SELECT hash from users where username = ?";
		else
		{
			$user = self::GetID($user);
			$sql = "SELECT hash from users where user_id = ?";
		}
		$statement = $dbc->prepare($sql);
		$statement->execute([$user]);

		$user = $statement->fetchObject(__CLASS__);

		if($user == false)
			return false;

		$return = false;
		if(password_verify($password, $user->hash))
		{
			// User password matches!
			$return = true;
			// Does the password hash need updating?
			if(password_needs_rehash($user->hash, PASSWORD_DEFAULT, ['cost' => AUTH_PASS_COST]))
				$user->SetPassword($dbc, $password);
		}

		return $return;
	}

	private static function GetID($user)
	{
		if($user instanceof User)
			return $user->user_id;
		else if(is_numeric($user))
			return $user;

		throw new Exception("Bad user index input");
	}
}?>