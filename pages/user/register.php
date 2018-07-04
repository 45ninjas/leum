<?php
require_once SYS_ROOT . "/core/leum-core.php";
/**
* Default page
*/
class register implements IPage
{
	private $title;
	private $success = false;

	private $errors;
	private $inputs;

	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$leum->PermissionCheck("register-account");
		$this->title = "Register Account";

		if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email']))
		{
			$this->inputs = array(
				'username'	=> $_POST['username'],
				'email'		=> $_POST['email']
			);
			if(UserAccount::CreateUser($dbc, $_POST['username'], $_POST['password'], $_POST['email'], $this->errors))
			{
				// The user was created. Redirect to login page.
				$msg = "Your account was successfully created. Please login.";
				$leum->Redirect("/" . LOGIN_URL . "?info=$msg");
			}
		}

		$leum->SetTitle($this->title);

		// Set-up the errors.
		if(isset($this->errors['username']))
			Message::Create("msg-red", $this->errors['username'], "username");
		if(isset($this->errors['password']))
			Message::Create("msg-red", $this->errors['password'], "password");
		if(isset($this->errors['email']))
			Message::Create("msg-red", $this->errors['email'], "email");
	}
	public function Content()
	{
?>

<div class="main">
	<div class="header">
		<h1><?=$this->title;?></h1>
	</div>
	<div class="content login-box">
		<form class="pure-form" method="POST">
			<fieldset class="pure-group">
				<input class="pure-input-1" id="username" type="text" name="username" placeholder="Username" <?php if(isset($this->inputs)) echo "value=\"" . $this->inputs['username'] . "\""; ?>>
				<?php Message::ShowMessages("username"); ?>
				<input class="pure-input-1" type="password" name="password" placeholder="Password">
				<?php Message::ShowMessages("password"); ?>
				<input class="pure-input-1" type="text" name="email" placeholder="Email Address" <?php if(isset($this->inputs)) echo "value=\"" . $this->inputs['email'] . "\""; ?>>
				<?php Message::ShowMessages("email"); ?>
			</fieldset>
			<label for="check2" class="pure-checkbox" for="check2">
				<input id="check2" type="checkbox" name="check2"> I understand that <strong>security</strong> and <strong>privacy</strong> cannot be guaranteed and are not regarded as high priorities in the current state of development of leum.
			</label>
			<button type="submit" name="create-user" class="pure-button pure-button-primary">Create User</button>
		</form>
	</div>
</div>

<?php
	}
}
?>