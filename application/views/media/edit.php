<?php

class View implements Viewable
{
	public function TheTitle()
	{
		echo "Edit Media";
	}
	public function __construct($arguments)
	{

	}

	public function TheContent()
	{?>
	<div class="content">
		<h2>Edit Media</h2>

		<form class="pure-form">
			<fieldset>
				<legend>Add Media</legend>

				<input placeholder="Title" name="title">
				<input placeholder="Path" name="path">
				<input placeholder="Source" name="source">

				<button type="submit" class="pure-button pure-button-primary">Add</button>
			</fieldset>
		</form>
	</div>
	<?php }

	public function TheHead()
	{

	}
}

?>