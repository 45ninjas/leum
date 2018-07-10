<?php
/**
* View Media
* TODO: Add pagination.
*/
include SYS_ROOT . "/leum/utils/importer.php";
class media_importer implements IPage
{
	private $items;
	private $directory;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$leum->PermissionCheck("admin-pages", "media-create", "tags-create", "media-tags");
		$leum->SetTitle("Import Utility");

		$leum->RequireResource('/resources/js/suggestive-input.js', '<script src="' . GetAsset('/resources/js/suggestive-input.js') . '"></script>');

		if(isset($_GET['directory']))
		{
			$this->directory = $_GET['directory'];
			ImportDirectory($this->directory, Leum::Instance()->dbc);
		}
	}
	public function Content()
	{ ?>

<div class="main">
	<div class="header">
		<div class="content">
			<h1>Import Utility</h1>
			<div class="pure-menu pure-menu-horizontal">
				<ul class="pure-menu">
					<li class="pure-menu-item"><a href="<?=ROOT."/edit/";?>" class="pure-menu-link">&#10094; Edit</a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="content">
		<?php ImportForm(); ?>
		<?php Message::ShowMessages("importer"); ?>
	</div>
</div>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", loadImporter);

	function loadImporter()
	{
		var apiUrl = document.head.querySelector("[property=api-url]").content;	

		var directoryBox = document.querySelector('#dir-box');
		var suggestiveInput = new SuggestiveInput(directoryBox);

		var xmlhttp = new XMLHttpRequest();

		xmlhttp.onreadystatechange = function()
		{
			if(this.status == 200 && this.readyState == 4)
			{
				var response = JSON.parse(this.responseText);
				if(response['error'])
					console.log(response);
				else
					suggestiveInput.SetSuggestions(response);
			}
		}

		directoryBox.addEventListener('suggestion-match', function(e)
		{
			var suggestion = e.detail.text();
			GetSuggestions(suggestion);
		});

		function GetSuggestions(suggestion)
		{
			if(suggestion != null)
				var url = apiUrl + "/v2/utilities/directories?dir=" + suggestion;
			else
				var url = apiUrl + "/v2/utilities/directories";

			xmlhttp.open("GET", url, true);
			xmlhttp.send();
		}

		GetSuggestions();
	}
</script>

<?php }
}

function ImportForm()
{
	?>
	<form class="pure-form">
		<div id="dir-box" class="suggestion-input">
			<input class="user-input" type="text" name="directory">
			<input class="suggestion" type="text" disabled>
		</div>
		<!-- <label class="pure-checkbox" for="recursive"><input type="checkbox" id="recursive" name="recursive" checked>Recursive scan</label> -->
		<input class="pure-button pure-button-primary" type="submit" name="list" value="Search">
	</form>
	<?php
}
function ShowItems()
{
	echo "<ul>";
	foreach ($items as $item)
	{
		echo "<li>$item</li>";
	}
	echo "</ul>";
}

?>
