<?php

include SYS_ROOT . '/leum/plugins/importer/import-functions.php';
class Import implements IPage
{
	private $title;
	private $imports;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		// Make sure the user has permission to create tags and media.
		$leum->PermissionCheck("admin-pages", "media-create", "tags-create", "media-tags");

		// Get the suggestive input script added to the head.
		$leum->RequireResource('/resources/js/suggestive-input.js', '<script src="' . GetAsset('/resources/js/suggestive-input.js') . '"></script>');

		$leum->RequireResource('/resources/js/media-viewer.js', '<script src="' . GetAsset('/resources/js/media-viewer.js') . '"></script>');
		$leum->RequireResource('tags.js', '<script type="text/javascript" src="' . GetAsset('/resources/js/tags.js') . '"></script>');

		// If the directory is set, try to import it.
		if(isset($_GET['directory']))
		{
			$this->directory = $_GET['directory'];
			$importer = new ImportUtility();
			$this->imports = $importer->ImportDirectory($this->directory);
		}

		// Set the title
		$this->title = "Import Utility";
		$leum->SetTitle($this->title);
	}
	public function Content()
	{ ?>
	<div class="main">
		<div class="header">
			<div class="content">
				<h1><?=$this->title?></h1>
				<div class="pure-menu pure-menu-horizontal">
					<ul class="pure-menu">
						<li class="pure-menu-item"><a href="<?=ROOT."/edit/";?>" class="pure-menu-link">&#10094; Edit</a></li>
					</ul>
				</div>
			</div>
		</div>
		
		<div class="content">
			<?php
				$this->ShowControls();
				Message::ShowMessages("importer");
				$this->ShowImports();
			?>
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
	<?php
	$this->MediaViewer();
	}

	function ShowControls()
	{
		?>
			<form class="pure-form">
				<div id="dir-box" class="suggestion-input">
					<input class="user-input" type="text" name="directory">
					<input class="suggestion" type="text" disabled>
				</div>
				<textarea name="description">Leum import utility import</textarea>
				<!-- <label class="pure-checkbox" for="recursive"><input type="checkbox" id="recursive" name="recursive" checked>Recursive scan</label> -->
				<input class="pure-button pure-button-primary" type="submit" name="list" value="Search">
			</form>
		<?php
	}
	function ShowImports()
	{
		if(!isset($this->imports))
			return;

		echo "<div class=\"items\">";
		foreach ($this->imports as $import)
		{
			?>
			<a id=view<?=$import['id']?> data-media-index="<?=$import['id']?>" class="item-tile" href="#view<?=$import['id']?>">
				<img src="<?=ROOT . THUMB_DIR . $import['thumbnail']?>">
			</a>
			<?php
		}
		echo "</div>";
	}
	function MediaViewer()
{ ?>
<div id="media-viewer" class="media-viewer full" hidden>
	<div class="media-viewer-modal">
		<a id="media-viewer-next" class="viewer-button" hidden>&rsaquo;</a>
		<a id="media-viewer-prev" class="viewer-button" hidden>&lsaquo;</a>
		<div class="header">
			<span id="media-title"></span>
			<button class="close-button" id="media-viewer-close"><i class="fa fa-times"></i></button>
		</div>
		<div class="content">
		</div>
		<div class="footer">
			<a id="media-edit-link" class="edit-button" href="#">
				<i class="fa fa-edit"></i>
			</a>
			<div class="tag-input">
				<input id="tag-input" type="hidden" name="tags" value="">
				<input type="text" id="tag-input-field" placeholder="new tag">
				<ul class="suggestion-box" id="suggestion-box" hidden>
				</ul>
			</div>
			<div id="tag-editor-field" class="tags tag-field">
			</div>
		</div>
	</div>
</div>
<?php }
}
?>