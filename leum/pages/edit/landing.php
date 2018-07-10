<?php
/**
* Edit Landing page
*/
class landing implements IPage
{
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$leum->PermissionCheck("admin-pages");
		$leum->SetTitle("Edit");
	}
	public function Content()
	{ ?>

<div class="main">
	<div class="header">
		<div class="content">
			<h1>Edit</h1>
		</div>
	</div>
	<div class="content">
		<h2>Edit Media, tags and what not.</h2>
		
		<ul class="leum-blank-list">
			<li><a href="<?=ROOT?>/edit/media/">Media</a></li>
			<li><a href="<?=ROOT?>/edit/tag/">Tags</a></li>
			<li><a href="<?=ROOT?>/edit/user/">Users</a></li>
			<li><a href="<?=ROOT?>/edit/permissions/">Permissions</a></li>
		</ul>
	</div>
</div>

<?php }
}
?>
