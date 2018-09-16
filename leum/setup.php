<?php

if(!defined('SYS_ROOT'))
	define('SYS_ROOT', realpath(__DIR__ . '/../'));

require_once SYS_ROOT . "/leum/functions.php";
require_once SYS_ROOT . "/leum/core/leum-core.php";

LeumSetup::Init();

class LeumSetup
{
	public static $dbc;
	public static $leumCore;
	public static $currentStep;

	public static $steps = [
		"initial" => [
			"function" => 'LeumSetup::InitalStep',
			"title" => "Start"
		],
		"verify" => [
			"function" => 'LeumSetup::CheckStep',
			"title" => "Verify Prerequisites"
		],
		"install" => [
			"function" => 'LeumSetup::SetupProcess',
			"title" => "Install"
		],
		"root-user" => [
			"function" => 'LeumSetup::RootUserStep',
			"title" => "Create User",
			"disable-button" => true,
		],
		"finalize" => [
			"function" => 'LeumSetup::Finalize',
			"title" => "Finalize Setup"
		]
	];

	static function SetupProcess()
	{
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
		self::CreateTable("tag_map",	'TagMap',		'CreateTable');

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
			if($e->GetCode() == "23000")
			{
				Message::Create("warning", "Role or Permission already exists.");
				Log::Write("A role or permission already exists.", LOG::WARNING, "setup.txt");
			}
			else
				throw $e;
				
		}
	}

	static function Init()
	{
		// Get the current step from the post. If no, make it initial.
		if(isset($_POST['step']))
		{
			LeumSetup::$currentStep = $_POST['step'];

			if($_POST['step'] === "finalize")
			{
				// Looks like we failed to create the user.
				if(!LeumSetup::TryMakeUser())
					LeumSetup::$currentStep = "root-user";
				else
					Message::Create("success", "Successfully created the root user.");
			}
		}
		else
			LeumSetup::$currentStep = "initial";


		if(!isset(LeumSetup::$steps[LeumSetup::$currentStep]))
		{
			throw new Exception("Invalid Step");
		}
	}
	static function TryMakeUser()
	{
		// Make sure the passwords exist.
		if(!isset($_POST['password']) || !isset($_POST['verify']))
		{
			Message::Create("error", "Password cannot be empty.");
			return flase;
		}
		// Make sure the two passwords where the same.
		if($_POST['password'] != $_POST['verify'])
		{
			Message::Create("error", "Passwords don't match.");
			return false;
		}
		$userId = null;

		$dbc = DBConnect();
		// Attempt to create the user. If fails. Go back to the user creation page.
		if(!UserAccount::CreateUser($dbc, $_POST['username'], $_POST['password'], $_POST['email'],$errors, $userId))
		{
			// Create the messages from our attempt to create a user.
			foreach ($errors as $error)
				Message::Create("error", $error);

			return false;
		}

		UserRoleMap::Map($dbc, $userId, "root");
		return true;
	}

	static function CreateUser()
	{

	}

	static function DoStep()
	{
		call_user_func(LeumSetup::$steps[LeumSetup::$currentStep]["function"]);
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

	static function RootUserStep()
	{
		?>
		<form class="user" method="POST">
			<div>
				<label for="username">User name: </label>
				<input id="username" type="text" name="username">
			</div>
			<div>
				<label for="email">Email (optional): </label>
				<input id="email" type="email" name="email">
			</div>
			
			<div>
				<label for="pass">Password: </label>
				<input id="pass" type="password" name="password">
			</div>
			<div>
				<label for="verify-pass">Verify: </label>
				<input id="verify-pass" type="password" name="verify">
			</div>

			<input type="hidden" name="step" value="finalize">
			<div class="right"><input class="next-button" type="submit" value="Finalize Setup"></div>
		</form>
		<?php
	}
	static function CheckStep()
	{
		// Can leum write to the log directory.
		Message::Create("msg","Verifying permission to write to " . LOG_DIR);
		if(is_writable(LOG_DIR))
			Message::Create("success", "Can write to the logs directory.");
		else
			Message::Create("error", "Unable to write to the logs directory");

		// Can leum write to the media directory.
		Message::Create("msg","Verifying permission to write to " .SYS_ROOT . MEDIA_DIR);
		if(is_writable(SYS_ROOT . MEDIA_DIR))
			Message::Create("success", "Can write to the media directory.");
		else
			Message::Create("warning", "Unable to write to the media directory. Some plugins might not work properly.");
		
		// Can leum write to the thumbnails directory.
		Message::Create("msg","Verifying permission to write to " .SYS_ROOT . THUMB_DIR);
		if(is_writable(SYS_ROOT . MEDIA_DIR))
			Message::Create("success", "Can write to the thumbnails directory.");
		else
			Message::Create("error", "Unable to write to the thumbnails directory");

		// Test the database connection.
		Message::Create("msg","Checking database connection");
		try
		{
			DBConnect();
			Message::Create("success", "Connected to the <i>". DB_NAME . "</i> database successfully.");
		}
		catch (Exception $e)
		{
			Message::Create("error", "Unable to connect to the database. ". $e->GetMessage());
		}

		Message::Create("msg","Checking FFprobe and FFmpeg");

		exec("ffmpeg -version", $out, $exit_code);
		if($exit_code == 0)
		{
			exec("ffprobe -version", $out, $exit_code);
			if($exit_code == 0)
				Message::Create("success", "FFmpeg and FFprobe is installed.");
			else
				Message::Create("warning", "FFprobe does not exist. Generating thumbnails for video files will not work.");
		}
		else
		{
			Message::Create("warning", "FFmpeg does not exist. Generating thumbnails for video files will not work");
		}
	}
	static function InitalStep()
	{
		?>
		<div class="msg exception">If you have an existing leum database please back it up now.</div>
		<div class="warning msg">This setup process has NO upgrade functionality yet. Upgrading leum using this setup page is NOT recommended.</div>

		<h3>Firstly</h3>
		<ul>
			<li>Create a database and configure the <span class="debug">leum/conf/database.conf.php</span> file to match your database settings.</li>
			<li>Update the <span class="debug">leum/conf/leum.conf.php</span> file to match your liking.</li>
			<li>Setup symlinks for your media. And optionally, for your thumbnails.</li>
		</ul>
		<p>Feel free to browse the documentation available over at <a href="https://leum.readthedocs.io/en/latest/setup.html">readthedocs.org</a> or <a href="https://leum.tomp.id.au/docs/setup.rst">here</a>.</p>
		<?php
	}
	static function Finalize()
	{
		?>
		<h2>Leum has been successfully set-up</h2>
		<p>You can now login with your new root user <a href="<?=ROOT . "/" . LOGIN_URL?>">here</a>. Alternatively you can import your existing media library by using the <a href="<?=ROOT?>/edit/import/">media import tool</a>.</p>
		<?php
	}
}
?>
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

		.msg
		{
			background-color: #c9c9c9;
			padding: 0.4em;
		}
		.info
		{
			background-color: #86c8ec;
		}
		.success
		{
			background-color: #56c645;
		}
		.error
		{
			background-color: #e37f7f;
		}
		.warning
		{
			background-color: #ffab00;
		}
		.exception
		{
			background-color:#860000;
			font-weight: bold;
			color: #FFF;
		}
		.debug
		{
			font-family: monospace;
			background: #EEE;
			display: inline-block;
			padding: 2px;
		}

		.steps
		{
			display: flex;
			margin-bottom: 3em;
			margin-top: 3em;
		}
		.step
		{
			flex: 1;
			padding: 0.5em;
			text-align: center;
			margin: 0.5em;
			background-color: #dfe8f8;
			font-weight: bold;
			color: #7e7e7e;
		}
		.step.current
		{
			background-color: #accaff;
			color: black;
		}
		.right
		{
			text-align: right;
		}
		form .next-button
		{
			width: 20em;
			border:none;
			padding: 0.5em;
			margin: 0.5em;
			background-color: #accaff;
			font-size: inherit;
		}
		.user div label
		{
			width: 140px;
			display: inline-block;
			text-align: right;
		}
	</style>
</head>
<body>
	<main>
		<div class="setup">
			<h2>Leum - Setup</h2>
			<a href="../docs/setup.rst">Documentation</a>
			<?php
			echo "<div class=\"steps\">";
			foreach (LeumSetup::$steps as $key => $step)
			{
				$current = "";
				if($key == LeumSetup::$currentStep)
					$current = " current";

				echo "<span class=\"step$current\">" . $step['title'] . "</span>";
			}
			echo "</div>";

			Message::ShowMessages();
			LeumSetup::DoStep();
			Message::ShowMessages();

			$next = null;
			$nextTitle = null;

			while ($key = key(LeumSetup::$steps) != null)
			{
				$key = key(LeumSetup::$steps);
				next(LeumSetup::$steps);

				if($key == LeumSetup::$currentStep)
				{
					$next = key(LeumSetup::$steps);
					if(isset($next))
					{
						$nextTitle = LeumSetup::$steps[$next]['title'];
						if(isset(LeumSetup::$steps[$key]['disable-button']) && LeumSetup::$steps[$key]['disable-button'] == true)
							$next = null;
					}
				}
			}

			if($next !== null)
			{
			?>
			<form method="POST">
				<input type="hidden" name="step" value="<?=$next?>">
				<div class="right"><input class="next-button" type="submit" value="<?=$nextTitle?>"></div>
			</form>
			<?php
			}
			?>
		</div>
	</main>	
</body>
</html>