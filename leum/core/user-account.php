<?php 
define('DUPLICATE_ERROR', 1062);
class UserAccount
{
	public static function Login($dbc, $user, $password)
	{
		if($user instanceof User)
			$user = $user->username;

		$statement = $dbc->prepare("SELECT hash FROM users WHERE username = ?");
		$statement->execute([$user]);

		$hash = $statement->fetchColumn();

		// Is there a user and does the user's hash match the password?
		if($hash != false && password_verify($password, $hash))
		{
			// looks like the user's password was correct. Update their last login and
			// password hash if needed.
			$arguments = array($user);
			$rehash = "";

			// Do we need to rehash the user's password?
			if(password_needs_rehash($hash, PASSWORD_DEFAULT, ['cost' => AUTH_PASS_COST]))
			{
				$hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => AUTH_PASS_COST]);
				$rehash = ", hash = ?";
				array_push($arguments, $hash);
			}

			// Apply the last_login and hash changes.
			$statement = $dbc->prepare("UPDATE users SET last_login = NOW()$rehash WHERE username = ?");
			$statement->execute($arguments);

			return true; // User's password matches.
		}

		return false; // User does not exist or their password is wrong.
	}
	public static function ForgotPassword($dbc, $email)
	{
		throw new Exception('not implemented');
	}

	public static function CreateUser($dbc, $username, $password, $email, &$errors)
	{
		$errors = array();

		// Make sure the user entered valid information.
		$valid = true;
		if(strlen($password) < 8)
		{
			$errors['password'] = "Password must be 8 or more characters long.";
			$valid = false;
		}
		if(strlen($username) < 3)
		{
			$errors['username'] = "Username must be 3 or more characters long.";
			$valid = false;
		}
		elseif(!LeumCore::IsValidUsername($username))
		{
			$errors['username'] = "Username can only contain alphanumeric and some special characters like ! &amp; _ and -.";
			$valid = false;
		}
		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$errors['email'] = "Email is not an email.";
			$valid = false;
		}

		if(!$valid)
		{
			return false;
		}

		// Generate a password hash.
		$hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => AUTH_PASS_COST]);

		// Insert them into the database.
		try
		{
			// Get the new_user_role id.
			$statement = $dbc->prepare("SELECT role_id FROM roles WHERE slug = ?");
			$statement->execute([NEW_USER_ROLE]);
			$roleId = $statement->fetchColumn();

			// Actually insert the user into the database.
			$statement = $dbc->prepare("INSERT INTO users (email, username, hash) VALUES (?, ?, ?)");
			$statement->execute([$email, $username, $hash]);
			$userId = $dbc->lastInsertId();

			// Assign the new user role.
			var_dump($roleId);
			UserRoleMap::Map($dbc, $userId, $roleId);

			return true;
		}
		catch(PDOException $e)
		{
			// Is the error a duplicate error?
			if($e->errorInfo[1] == DUPLICATE_ERROR)
			{
				// Get the error message and get the part that tells us
				// what column is at fault.
				$msg = $e->errorInfo[2];
				preg_match('/key\ \'([a-z]+)\'/', $msg, $matches);

				// We are expecting only 'username' or 'email'. If anything (and nothing)
				// else shows up then throw up.
				if(isset($matches[1]))
				{
					$field = $matches[1];
					if($field == 'username')
						$errors['username'] = "Username is already in use.";
					elseif($field == 'email')
						$errors['email'] = "Email is already in use.";
					else
						throw $e;
				}
				else
					throw $e;
			}
			// Looks like the error is worse than we expected! It's NOT a DUPLICATE_ERROR!
			else
				throw $e;
		}

		return false;
	}
}
?>