<?php
require_once SYS_ROOT . "/core/leum-core.php";
/**
* Default page
*/
class login implements IPage
{
	private $title;
	private $forgot = false;
	private $success = false;
		
	private $forgotSucessMsg = "We've sent you an email.";

	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$this->title = "Login";

		if(isset($arguments[0]) && $arguments[0] == "forgot")
		{
			$this->forgot = true;
			$this->title = "Reset Account";
		}

		if(isset($_GET['info']))
			Message::Create("msg-blue", $_GET['info'], "login");

		if(isset($_POST["reset-email"]))
			Message::Create("msg-blue", $this->forgotSucessMsg, "login");

		if(isset($_POST["login"]))
		{
			$password = $_POST["password"];
			$username = $_POST["username"];

			if($leum->AttemptLogin($username, $password, $message))
				$this->success = true;
			else
				Message::Create("msg-red", $message, "login");
		}

		$leum->SetTitle($this->title);
	}
	public function Content()
	{
?>

<div class="main">
	<div class="header">
		<h1><?=$this->title;?></h1>
	</div>
	<div class="content login-box">
		<?php if($this->forgot) $this->ShowReset(); else $this->ShowLogin(); ?>
	</div>
</div>

<?php
	}
	function ShowLogin()
	{
		Message::ShowMessages("login");
		?>
		<form class="pure-form" action="<?=ROOT;?>/login" method="POST">
			<fieldset class="pure-group">
				<input type="text" class="pure-input-1" placeholder="Username" name="username">
				<input type="password" class="pure-input-1" placeholder="Password" name="password">
			</fieldset>
			<a href="<?=ROOT . "/" . LOGIN_URL?>/forgot">Having troubles logging in?</a>
			<br>
			<a href="<?=ROOT?>/register">Don't Have an account?</a>
			<label class="pure-checkbox" for="remember-me">
				<input id="remember-me" type="checkbox" name="remember">
				Remember me.
			</label>
			<button type="submit" name="login" class="pure-button pure-input-1 pure-button-primary">Sign In</button>
		</form>
		<?php
	}
	function ShowReset()
	{
		?>
		<p>We'll send you an email to reset your account.</p>
		<form class="pure-form" action="<?=ROOT . "/" . LOGIN_URL;?>/forgot" method="POST">
			<input type="email" class="pure-input-1" name="reset-email" placeholder="Email">
			<button type="submit" class="pure-button pure-input-1 pure-button-primary">Send Email</button>
		</form>
		<?php
	}
}
?>
