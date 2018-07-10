	<div class="title-menu">
		<a class="item title-menu-heading"><?=APP_TITLE?></a>
		<div class="item user-button" id="user-dropdown-button">
			<?php ProfilePicture(); ?>
			<?php if(isset($leum->user)): ?>
			<div class="user-dropdown" id="user-dropdown" hidden="">
				<div class="user-header">
					<?php ProfilePicture(); ?>
					<div class="details">
						<span class="username"><?=$leum->user->username?></span>
						<span class="email"><?=$leum->user->email?></span>
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
			<?php else: ?>
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
			<?php endif; ?>
		</div>
	</div>