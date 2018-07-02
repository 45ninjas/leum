<?php
/**
* Edit Landing page
*/
class landing implements IPage
{
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$leum->SetTitle("Edit");

		if(!$leum->AllowedTo('admin-pages'))
		{
			$leum->ShowPermissionErrorPage("You are not allowed to do this.");
			//throw new exception("You are not allowed to do this");
		}
	}
	public function Content()
	{ ?>

<div class="main">
	<div class="header">
		<h1>Edit</h1>
	</div>
	<div class="content">
		<h2>Edit Media, tags and what not.</h2>
		
		<ul class="leum-blank-list">
			<li><a href="<?=ROOT?>/edit/media/">Media</a></li>
			<li><a href="<?=ROOT?>/edit/tag/">Tags</a></li>
			<li><a href="<?=ROOT?>/edit/user/">Users</a></li>
			<li><a href="<?=ROOT?>/edit/permissions/">Permissions</a></li>
		</ul>

		<code class="leum-code leum-green">
			<pre>//The pages in edit should be protected by user authentication and permissions
//as the pages in edit are very powerful, Letting anyone in would cause some
//damage.
//TODO: Protect the edit pages with user authentication and/or permissions.
//TODO: Add user authentication.</pre>
		</code>
	</div>
</div>

<?php }
}
?>
