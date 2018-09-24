<?php
/**
* Default page
*
* All pages must have the same class name as file name.
*/
class default_page implements IPage
{
	private $header;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		// Set leum's title.
		$title = "Default Page";
		$leum->SetTitle($title);

		// Create the arguments for the header widget.
		$args = [
			'title'		=> $title,
			'subtitle'	=> "Default Page"
		];

		// Create the header widget with the above arguments.
		$this->header = Front::GetWidget('page_header', $args);
	}
	public function Content()
	{ ?>

<div class="main">
	<?php
	// Display the header created above.
	if($this->header != false)
		$this->header->Show();
	?>
	<div class="content">
		<p>This page has no content as it's a <i>Default Page</i>. It's only an example.</p>
	</div>
</div>

<?php }
}
?>
