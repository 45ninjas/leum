<?php
require_once SYS_ROOT . "/core/leum-core.php";
/**
* Default page
*/
class Page
{
	private $title;
	private $messageText;
	private $messageClass = "msg-red";
	private $forgot = false;
	private $success = false;
	
	private $forgotMsg = "The provided email address does not match any records.";
	private $forgotSucessMsg = "We've sent you an email.";
	private $suspendedMsg = "Your account has been suspended.";
	private $failLoginMsg = "The provided user-name or password is incorrect. Please try again.";

	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$this->title = "Login";

		if(isset($arguments[0]) && $arguments[0] == "forgot")
		{
			$this->forgot = true;
			$this->title = "Reset Account";
		}

		if(isset($_POST["reset-email"]))
		{
			$this->messageClass = "msg-blue";
			$this->messageText = $this->forgotSucessMsg;
		}
		if(isset($_POST["login"]))
		{
			$password = $_POST["password"];
			$username = $_POST["username"];

			if($leum->AttemptLogin($username, $password))
			{
				$this->success = true;
			}
			else
			{
				$this->messageText = $this->failLoginMsg;
			}
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
		<?php $this->ShowMessage($this->messageText); ?>
		<?php if($this->forgot) $this->ShowReset(); else $this->ShowLogin(); ?>
	</div>
</div>

<?php
	}

	function ShowMessage($messageText)
	{
		if(!empty($messageText))
			echo "<div class=\"msg $this->messageClass\">$messageText</div>";
	}

	function ShowLogin()
	{
		?>
		<form class="pure-form" action="<?=ROOT;?>/login" method="POST">
			<fieldset class="pure-group">
				<input type="text" class="pure-input-1" placeholder="Username" name="username">
				<input type="password" class="pure-input-1" placeholder="Password" name="password">
			</fieldset>
			<a href="<?=ROOT?>/login/forgot">Having troubles logging in?</a>
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
		<form class="pure-form" action="<?=ROOT;?>/login/forgot" method="POST">
			<input type="email" class="pure-input-1" name="reset-email" placeholder="Email">
			<button type="submit" class="pure-button pure-input-1 pure-button-primary">Send Email</button>
		</form>
		<?php
	}
}
?>
