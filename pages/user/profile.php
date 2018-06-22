<?php
/**
* Default page
*/
class Page
{
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$leum->SetTitle("Profile");
	}
	public function Content()
	{ ?>

<div class="main">
	<div class="header">
		<h1>$userInfo->user->username</h1>
	</div>
	<div class="content">
		<p>This page has no content yet.</p>
	</div>
</div>

<?php }
}
?>
