<?php

/**
 * Title bar
 */
class Title_Bar implements IWidget
{
	public $menu = null;
	function __construct($args)
	{
		if(isset($args['menu']))
			$this->menu = $args['menu'];
	}

	private function ProfilePicture($colour = "#4eb4f5")
	{
		if(isset(Leum::Instance()->user))
		{
			$profileUrl = GetAsset("/resources/graphics/default-profile-1.png");
			echo "<img class=\"profile\" src=\"$profileUrl\" style=\"background-color: $colour\">";
		}
		else
			echo "<i class=\"fa fa-user-circle\"></i>";
	}

	public function Show()
	{
		?>
	<div class="title-menu">
		<!-- <a class="item title-menu-heading"><?=APP_TITLE?></a> -->
		<?php
		if(isset($this->menu)) $this->menu->Show(); ?>
		<div class="item user-button" id="user-dropdown-button">
			<?php ProfilePicture(); ?>
			<?php $this->UserDropdown(); ?>
		</div>
	</div>
		<?php
	}

	public function UserDropdown()
	{
		$user = Leum::Instance()->user;
		if(isset($user)) : ?>
			<div class="user-dropdown" id="user-dropdown" hidden="">
				<div class="user-header">
					<?php ProfilePicture(); ?>
					<div class="details">
						<span class="username"><?=$user->username?></span>
						<span class="email"><?=$user->email?></span>
					</div>
				</div>
				<ul>
					<li>
						<a href="<?=ROOT?>/profile"><i class="fa fa-user-circle" aria-hidden="true"></i><span>Profile</span></a>
					</li>
					<li>
						<a href="<?=ROOT?>/settings"><i class="fa fa-sliders" aria-hidden="true"></i><span>Settings</span></a>
					</li>
					<hr>
					<li>
						<a href="?logout"><i class="fa fa-sign-out" aria-hidden="true"></i><span>Sign Out</span></a>
					</li>
				</ul>
			</div>
		<?php else : ?>
			<div class="user-dropdown" id="user-dropdown" hidden="">
				<form class="pure-form" action="<?=ROOT . "/" . LOGIN_URL;?>" method="POSt">
					<fieldset class="pure-group">
						<input type="text" class="pure-input-1" placeholder="Username" name="username">
						<input type="password" class="pure-input-1" placeholder="Password" name="password">
					</fieldset>
					<a class="regiser-link" href="<?=ROOT?>/register">Don't Have an account?</a>
					<button type="submit" class="pure-button pure-button-primary full-width" name="login">Sign In</button>
				</form>
			</div>
		<?php endif;
	}
}
?>
