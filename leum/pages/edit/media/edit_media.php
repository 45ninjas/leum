<?php
/**
* Default page
*/
require_once SYS_ROOT . '/leum/page-parts/media-viewer.php';
require_once SYS_ROOT . '/leum/page-parts/tag-field.php';
class edit_media implements IPage
{
	public $title = "Create Media";
	private $mediaItem;
	private $modify = false;
	private $viewer;

	private $tagField;

	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$leum->PermissionCheck("admin-pages", "media-edit");

		// Why is this up here separated from the rest?
		$mediaId = null;
		if(isset($arguments[0]) && is_numeric($arguments[0]))
		{
			$this->modify = true;
			$mediaId = $arguments[0];
		}

		if(isset($_POST['modify']) && isset($_POST['title']) && isset($_POST['file']) && isset($_POST['description']) && isset($_POST['tags']))
		{
			$this->mediaItem = new Media();
			$this->mediaItem->title = $_POST['title'];
			$this->mediaItem->file = $_POST['file'];
			$this->mediaItem->description = $_POST['description'];

			$tags = ParseSlugString($_POST['tags']);
			$index = Media::InsertSingle($dbc, $this->mediaItem,$mediaId);
			TagMap::SetMappedTags($dbc, $index, $tags, true);

			if($_POST['modify'] === "generate-thumbnail")
			{
				include_once SYS_ROOT . "/utils/thumbnails.php";
				Thumbnails::MakeFor($dbc, $this->mediaItem);
			}
		}

		if(isset($mediaId))
		{
			$this->mediaItem = Media::GetSingle($dbc, $mediaId);
			if($this->mediaItem == null)
			{
				$leum->Show404Page("Media item $mediaId does not exist in the database.");
				return;
			}
			$tags = $this->mediaItem->GetTags($dbc, true);
			$this->title = "Edit Media";
		}
		else
			$this->mediaItem = new Media();

		$this->viewer = new MediaViewer($this->mediaItem, true);

		$leum->RequireResource('tags.js', '<script type="text/javascript" src="' . GetAsset('/resources/js/tags.js') . '"></script>');
		$leum->SetTitle($this->title);

		$this->tagField = Front::GetWidget("tag_field", [
			"tags" => $tags,
			"read only" => false
		]);
	}
	public function Content()
	{ ?>

	<div class="main">
		<div class="header">
			<div class="content">
				<h1><?php echo $this->title; ?></h1>				
			</div>
		</div>
		<div class="content">
			<form id="media-edit" class="pure-form pure-form-stacked" method="post">
				<fieldset>
					<legend>Media Information</legend>
					<div class="pure-g">

						<div class="pure-u-1 pure-u-sm-1-2">
							<label for="title">Title</label>
							<input class="pure-u-1" tabindex="1" type="text" name="title" id="title" placeholder="Title" <?php $this->EchoValue($this->mediaItem->title); ?>>

							<label for="file">File</label>
							<input class="pure-u-1" tabindex="2" type="text" name="file" id="file" placeholder="Path" <?php $this->EchoValue($this->mediaItem->file); ?>>

							<label for="description">Description</label>
							<textarea class="pure-u-1 leum-textarea" tabindex="3" name="description" id="description" placeholder="Description"><?php if($this->modify) echo $this->mediaItem->description; ?></textarea>

							<label><?php echo "id: ". $this->mediaItem->id; ?></label>
						</div>

						<div class="pure-u-1 pure-u-sm-1-2">
							<?php $this->viewer->ShowPreview(); ?>
						</div>

					</div>
				</fieldset>

				<label for="tag-input-field">Tags</label>
				<?php $this->tagField->Show(); ?>
				<!-- <div class="tag-input">
					<input class="pure-u-1" tabindex="4" type="text" id="tag-input-text" placeholder="tag">
					<input id="tag-input" type="hidden" name="tags" value="<?=$this->tagString;?>">
					<ul class="suggestion-box" id="suggestion-box" hidden>
					</ul>
				</div>
				<div id="tag-field" class="tags tag-field">
				</div> -->

				<button form="media-edit" tabindex="4" type="submit" name="modify" class="pure-button pure-button-primary"><?php if($this->modify) echo "Apply"; else echo "Create";?></button>
				<?php if($this->modify): ?>
				<a class="pure-button button-delete" tabindex="5" data-title="<?php echo $this->mediaItem->title; ?>" href="<?php echo ROOT."/api/v2/media/" .$this->mediaItem->id; ?>">
					<i class="fa fa-trash"></i>
					Delete
				</a>
				<button form="media-edit" tabindex="6" type="submit" name="modify" value="generate-thumbnail" class="pure-button">Generate Thumbnail</button>
				<script type="text/javascript" src="<?php Asset("/resources/js/deleter.js");?>"></script>
				<?php endif; ?>
			</form>
			<script type="text/javascript">
				window.onload = function()
				{
					var tags = document.querySelector("#tag-input");
					console.log(tags);
					var input = document.querySelector("#tag-input-text");
					var field = document.querySelector("#tag-field");
					var suggestionBox = document.querySelector("#suggestion-box");

					tagEditor = new TagEditor(input, field, tags, suggestionBox);
					tagEditor.allowNew = true;
					tagEditor.SetTags(tags.value.split(','));
				}
			</script>
		</div>
	</div>

<?php }
	function EchoValue($value)
	{
		if($this->modify)
		echo "value=\"$value\"";
	}
}
?>
