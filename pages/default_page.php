<?php
/**
* Default page
*/
class default_page implements IPage
{
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$leum->SetTitle("Default Page");
	}
	public function Content()
	{ ?>

<div class="main">
	<div class="header">
		<h1>Default Page</h1>
	</div>
	<div class="content">
		<p>This page has no content yet.</p>
	</div>
</div>

<?php }
}
?>
