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
		{
			var_dump($this);
			throw new Exception("User's permissions are not set. Use 'GetPermissions()'");
		}

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

        $this->permissions = $statement->fetchAll(PDO::FETCH_COLUMN);
        return $this->permissions;
	}
	public function GetRoles($dbc)
	{
		$sql = "SELECT DISTINCT r.slug
		FROM roles r
		JOIN user_role_map urm ON r.role_id = urm.role_id
		JOIN users ON urm.user_id = users.user_id
		WHERE users.user_id = ?;";

		$statement = $dbc->prepare($sql);
		$statement->execute([$this->user_id]);

		$this->roles = $statement->fetchAll(PDO::FETCH_COLUMN);
		return $this->roles;
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
			user_id int unsigned auto_increment PRIMARY KEY,
			username varchar(255) NOT NULL UNIQUE,
			hash varchar(255) NOT NULL,
			last_login datetime NOT NULL,
			email text UNIQUE
		)";

		$dbc->exec($sql);
	}
	// Getting one user.
	public static function GetSingle($dbc, $user, $withPermissions = false, $everything = false)
	{
		if($everything)
			$columns = "*";
		else
			$columns = "u.user_id, u.username, u.last_login";

		if(is_string($user))
			$field = 'username';
		else
		{
			$field = 'user_id';
			$user = self::GetID($user);
		}

		if(!$withPermissions)
			$sql = "SELECT $columns from users as u where u.$field = ?";
		else
			$sql = "SELECT $columns,
			(SELECT DISTINCT GROUP_CONCAT(p.slug)
				FROM permissions p
				JOIN role_permission_map rpm ON p.permission_id = rpm.permission_id
				JOIN roles ON rpm.role_id = roles.role_id
				JOIN user_role_map upm on rpm.role_id = upm.role_id
				JOIN users ON upm.user_id = users.user_id
				WHERE users.user_id = u.user_id)
			AS permissions,
			(SELECT DISTINCT GROUP_CONCAT(r.slug)
				FROM roles r
				JOIN user_role_map urm ON r.role_id = urm.role_id
				JOIN users ON urm.user_id = users.user_id
				WHERE users.user_id = u.user_id)
			AS roles
			FROM users AS u
			where u.$field = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$user]);

		$result = $statement->fetchObject(__CLASS__);

		if($withPermissions)
		{
			$result->permissions = explode(',', $result->permissions);
			$result->roles = explode(',', $result->roles);
		}

		return $result;
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
	public static function GetAll($dbc, $permissions = false)
	{
		if(!$permissions)
			$sql = "SELECT user_id, username, last_login, email from users";
		else
		{
			$sql = "SELECT u.username, u.user_id, u.last_login, u.email,
			(SELECT DISTINCT GROUP_CONCAT(p.slug)
			    FROM permissions p
			    JOIN role_permission_map rpm ON p.permission_id = rpm.permission_id
			    JOIN roles ON rpm.role_id = roles.role_id
			    JOIN user_role_map upm on rpm.role_id = upm.role_id
			    JOIN users ON upm.user_id = users.user_id
			    WHERE users.user_id = u.user_id)
			AS permissions,
			(SELECT DISTINCT GROUP_CONCAT(r.slug)
			    FROM roles r
			    JOIN user_role_map urm ON r.role_id = urm.role_id
			    JOIN users ON urm.user_id = users.user_id
			    WHERE users.user_id = u.user_id)
			AS roles
		    FROM users AS u;";
		}

		$statement = $dbc->prepare($sql);
		$statement->execute();

		if($permissions)
		{
			$users = array();
			$statement->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
			while ($user = $statement->fetch())
			{
				$user->roles = explode(', ', $user->roles);
				$user->permissions = explode(', ', $user->permissions);
				array_unshift($users, $user);
			}
			return $users;
		}
		else
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
		var_dump($user);
		//$user->SetPassword($password);
		var_dump($user);


		$user = User::GetSingle($dbc, $user);

		if($user == false)
			return false;

		$return = false;
		if(password_verify($password, $user->hash))
		{
			// User password matches!
			$return = true;
			// Does the password hash need updating?
			if(password_needs_rehash($user->hash, PASSWORD_DEFAULT, ['cost' => AUTH_PASS_COST]))
				$user->SetPassword($password, $dbc);
		}

		return $return;
	}

	public static function GetID($user)
	{
		if($user instanceof User)
			return $user->user_id;
		else if(is_numeric($user))
			return $user;

		throw new Exception("Bad user index input");
	}
}?>