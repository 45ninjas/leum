<?php
/**
* Default page
*/
class Page
{
	private $userInfo;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$leum->SetTitle("Profile");
		$this->userInfo = $userInfo;
	}
	public function Content()
	{ ?>

<div class="main">
	<div class="header">
		<h1><?=$this->userInfo->username?></h1>
	</div>
	<div class="content">
		<p>Hi <?=$this->userInfo->username?>, You last logged in at <?=$this->userInfo->last_login?>.</p>
		<h2 class="title"><i class="fa fa-key left"></i>Permissions</h2>
		<p>You have <?=count($this->userInfo->permissions)?> permissions associated with your account.</p>
		<ul>
		<?php foreach ($this->userInfo->permissions as $permission)
			echo "<li>$permission</li>";
		?>
		</ul>
	</div>
</div>

<?php }
}
?>
