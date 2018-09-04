<!DOCTYPE html>
<html>
<head>
	<title>Leum - Setup</title>
	<style type="text/css">
		.setup
		{
			padding: 2em;
			max-width: 1024px;
			margin: auto;
			background-color: #FFF;
		}
		body
		{
			font-family: sans-serif;
			background-color: #EEE;
		}

		.messages
		{
			font-family: monospace;
			background-color: #EEE;
		}

		.messages .msg
		{
			background-color: #c9c9c9;
			padding: 0.4em;
		}
		.messages .info
		{
			background-color: #86c8ec;
		}
		.messages .success
		{
			background-color: #56c645;
		}
		.messages .error
		{
			background-color: #e37f7f;
		}
		.messages .warning
		{
			background-color: #ffab00;
		}
		.messages .exception
		{
			background-color:#860000;
			font-weight: bold;
			color: #FFF;
		}
	</style>
</head>
<body>
	<main>
		<div class="setup">
			<h2>Leum - Setup</h2>
			<p>Make sure you have configured the <span class="messages">database.conf.php</span> config file, this along with other configuration files are available in <span class="messages">leun/conf/</span>.</p>
			<p>A setup log is available in <span class="messages">leum/logs/</span></p>
			<br>
			<hr>
			<h2>Setup Log</h2>
			<?php

			// Start the setup.
			LeumSetup::SetupProcess();

			// Show the messages.
			Message::ShowMessages("default");
			?>
		</div>
	</main>	
</body>
</html>

<?php

class LeumSetup
{
	public static $dbc;
	public static $leumCore;
	static function SetupProcess()
	{
		if(!defined('SYS_ROOT'))
			define('SYS_ROOT', realpath(__DIR__ . '/../'));

		require_once SYS_ROOT . "/leum/functions.php";
		require_once SYS_ROOT . "/leum/core/leum-core.php";
		require_once SYS_ROOT . "/leum/core/leum-core.php";
		// require_once SYS_ROOT . "/leum/co/user-permission/defaults.php";

		Message::Create("info", "starting setup process");
		Log::Write(" ==== starting setup process ==== ", LOG::INFO, "setup.txt");

		try
		{
			self::$dbc = DBConnect();		
			self::$leumCore = new LeumCore();
			self::SetupCore();
		}
		catch (Exception $e)
		{
			Message::Create("exception", $e);
			Log::Write($e, LOG::EXCEPTION, "setup.txt");
			return;
		}

		try
		{
			Message::Create("info", "setting up plugins");
			Log::Write("setting up plugins", LOG::INFO, "setup.txt");

			LeumCore::InvokeHook("leum.setup");
		}
		catch (Exception $e)
		{
			Message::Create("exception", $e);
			Log::Write($e, LOG::EXCEPTION, "setup.txt");
			return;
		}

		Message::Create("success", "setup process complete with no errors");
		Log::Write("setup process complete with no errors", LOG::INFO, "setup.txt");
	}

	static function SetupCore()
	{
		// Core functionality tables.
		self::CreateTable("media",		'Media',		'CreateTable');
		self::CreateTable("tags",		'Tag',			'CreateTable');
		self::CreateTable("map",		'Map',			'CreateTable');

		// Users and Roles.
		self::CreateTable("users", 'User', 'CreateTable');
		self::CreateTable("roles", 'Role', 'CreateTable');
		self::CreateTable("permissions",'Permission',	'CreateTable');
		self::CreateTable("role_permission_map", 'RolePermissionMap', 'CreateTable');
		self::CreateTable("user_role_map", 'UserRoleMap', 'CreateTable');

		// Default Roles.
		try
		{
			// Add all the permissions.
			$permissions = GetPermissions();
			$roles = GetRoles();

			foreach ($permissions as $slug => $description)
			{
				Permission::InsertSingle(self::$dbc, ["slug" => $slug, "description" => $description]);
			}
			Message::Create("info", "Added default permissions");
			Log::Write("Added default permissions", LOG::INFO, "setup.txt");

			// Add all the roles.
			foreach ($roles as $role => $slugs)
			{
				$title = array_shift($slugs);
				$role_id = Role::InsertSingle(self::$dbc, ["slug" => $role, "description" => $title]);
				RolePermissionMap::SetPermissions(self::$dbc, $role_id, $slugs[0]);

				Message::Create("info", "Added $role role");
				Log::Write("Added $role role", LOG::INFO, "setup.txt");
			}	
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	static function CreateTable($tableName, $class, $method)
	{
		if(self::TableExists($tableName))
		{
			Message::Create("warning", "$tableName already exists, skipping");
			Log::Write("$tableName already exists, skipping", LOG::WARNING, "setup.txt");
			return;
		}

		$class::$method(self::$dbc);
		Message::Create("success", "$tableName was created");
		Log::Write("$tableName was created", LOG::INFO, "setup.txt");
	}

	static function TableExists($tableName)
	{
		$sql = "SELECT * from information_schema.tables
		where table_schema = ? and table_name = ?
		limit 1";

		$statement = self::$dbc->prepare($sql);
		$statement->execute([DB_NAME, $tableName]);

		return $statement->fetch() != false;
	}
}

?>