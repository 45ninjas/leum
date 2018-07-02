<?php
require_once SYS_ROOT . "/core/user.php";
/**
* View Tags
*/
class edit_user implements IPage
{
	private $user;
	private $title;
	private $total;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$leum->PermissionCheck("admin-pages", "users-edit");
		$this->title = "Create User";
		$user_id = null;

		if(isset($arguments[0]))
		{
			$user_id = (int)$arguments[0];
			$this->title = "Edit User";
		}
		
		if(isset($_POST["create-user"]))
		{
			if(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"]))
			{
				$user = new User();
				$user->username = $_POST["username"];
				$user->email = $_POST["email"];
				$user->SetPassword($_POST["password"]);
				$user_id = User::InsertSingle($dbc, $user, $user_id);
			}
		}
		elseif(isset($user_id))
		{
			$this->user = User::GetSingle($dbc, $user_id);
		}

		$leum->SetTitle($this->title);
	}
	public function Content()
	{ ?>

<div class="main">
	<div class="header">
		<h1><?=$this->title;?></h1>
		<div class="pure-menu pure-menu-horizontal">
			<ul class="pure-menu">
				<li class="pure-menu-item"><a href="<?=ROOT."/edit/user";?>" class="pure-menu-link">&#10094; Users</a></li>
			</ul>
		</div>
	</div>

	<div class="content">
		<form class="pure-form pure-form-aligned" method="POST">
			<fieldset>
				<?php if(isset($this->user)): ?>
				<div class="pure-control-group">
					<label for="user-id">User ID</label>
					<input id="user-id" type="text" name="user-id" placeholder="Undefined" <?php $this->EchoValue($this->user->user_id); ?> readonly>
				</div>
				<?php endif; ?>
				<div class="pure-control-group">
					<label for="username">Username</label>
					<input id="username" type="text" name="username" placeholder="Username" <?php if(isset($this->user)) $this->EchoValue($this->user->username); ?>>
					<span class="pure-form-message-inline">Username is already in use.</span>
				</div>
				<div class="pure-control-group">
					<label for="password">Password</label>
					<input id="password" type="password" name="password" <?php if(isset($this->user)) echo "placeholder=\"New Password\""; else echo "placeholder=\"Password\""; ?> >
					<span class="pure-form-message-inline">8 characters or more.</span>
				</div>
				<div class="pure-control-group">
					<label for="email">Email Address</label>
					<input id="email" type="email" name="email" placeholder="Email Address" <?php if(isset($this->user)) $this->EchoValue($this->user->email); ?>>
					<span class="pure-form-message-inline">Invalid email address.</span>
				</div>
				<div class="pure-controls">
					<?php if($this->user == null): ?>
					<label for="check" class="pure-checkbox">
						<input id="check" type="checkbox">	I understand that leum is experimental and is no where close to a final product.
					</label>

					<label for="check2" class="pure-checkbox">
						<input id="check2" type="checkbox"> I understand that <strong>security</strong> and <strong>privacy</strong> cannot be guaranteed and are not regarded as high priorities in the current state of development of leum.
					</label>
					<?php endif ?>
					<button type="submit" name="create-user" class="pure-button pure-button-primary">Submit</button>
				</div>
			</fieldset>
		</form>
	</div>
</div>

<?php }

function EchoValue($value)
{
	if($this->user != null)
		echo "value=\"$value\"";
}

}
?>
