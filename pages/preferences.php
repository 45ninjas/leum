<?php
/**
* Default page
*/
class Page
{
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$leum->SetTitle("Preferences");
	}
	public function Content()
	{ ?>

<div class="main">
	<div class="header">
		<h1>User Preferences</h1>
	</div>
	<div class="content">
		<form class="pure-form">
			<fieldset>
				<label for="show-debug" class="pure-checkbox">
					<input type="checkbox" id="show-debug" name="show-debug" checked>
					Show Debugging Information.
				</label>
			</fieldset>
			<button class="pure-button" type="submit">Apply</button>
		</form>
	</div>
</div>

<?php }
}
?>
