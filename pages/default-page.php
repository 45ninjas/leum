<?php
/**
* Default page
*/
class Page
{
	public $title = "Default Page";
	public function __construct($arguments)
	{

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
