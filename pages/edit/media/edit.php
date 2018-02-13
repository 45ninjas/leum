<?php
/**
* Default page
*/
require_once SYS_ROOT . "/core/media.php";
require_once SYS_ROOT . "/core/mapping.php";
require_once SYS_ROOT . "/core/tag.php";

require_once 'page-parts/media-viewer.php';
require_once 'page-parts/tag-field.php';
class Page
{
	public $title = "Create Media";
	private $mediaItem;
	private $modify = false;
	private $viewer;
	private $tagField;

	public function __construct($arguments)
	{
		$dbc = Leum::Instance()->GetDatabase();

		// Why is this up here separated from the rest (ln 43)?
		$mediaId = null;
		if(isset($arguments[0]) && is_numeric($arguments[0]))
		{
			$this->modify = true;
			$mediaId = $arguments[0];
		}

		if(isset($_POST['modify']) && isset($_POST['title']) && isset($_POST['path']) && isset($_POST['source']) && isset($_POST['tags']))
		{
			$this->mediaItem = new Media();
			$this->mediaItem->title = $_POST['title'];
			$this->mediaItem->path = $_POST['path'];
			$this->mediaItem->source = $_POST['source'];

			$tags = ParseSlugString($_POST['tags']);
			$index = Media::InsertSingle($dbc, $this->mediaItem,$mediaId);
			Mapping::SetMappedTags($dbc, $index, $tags);

			if($_POST['modify'] === "generate-thumbnail")
			{
				include_once SYS_ROOT . "/utils/thumbnails.php";
				Thumbnails::MakeFor($dbc, $this->mediaItem);
			}
		}

		if(isset($mediaId))
		{
			$this->mediaItem = Media::GetSingle($dbc, $mediaId);
			$this->title = "Edit Media";

			if($this->mediaItem == null)
			{
				Leum::Instance()->Show404Page("Media item $mediaId does not exist in the database.");
				return;
			}
		}
		else
			$this->mediaItem = new Media();

		$this->viewer = new MediaViewer($this->mediaItem, true);
		$this->tagField = new TagField($this->mediaItem->GetTags());
	}
	public function Content()
	{ ?>

	<div class="main">
		<div class="header">
			<h1><?php echo $this->title; ?></h1>
		</div>
		<div class="content">
			<form id="media-edit" class="pure-form pure-form-stacked" method="post">
				<fieldset>
					<legend>Media Information</legend>
					<div class="pure-g">

						<div class="pure-u-1 pure-u-sm-1-2">
							<label for="title">Title</label>
							<input class="pure-u-1" tabindex="1" type="text" name="title" id="title" placeholder="Title" <?php $this->EchoValue($this->mediaItem->title); ?>>

							<label for="path">Path</label>
							<input class="pure-u-1" tabindex="2" type="text" name="path" id="path" placeholder="Path" <?php $this->EchoValue($this->mediaItem->path); ?>>

							<label for="source">Source</label>
							<textarea class="pure-u-1 leum-textarea" tabindex="3" name="source" id="source" placeholder="Source of Media item"><?php if($this->modify) echo $this->mediaItem->source; ?></textarea>

							<label><?php echo "media_id: ". $this->mediaItem->media_id; ?></label>
						</div>

						<div class="pure-u-1 pure-u-sm-1-2">
							<?php $this->viewer->ShowPreview(); ?>
						</div>

					</div>

					<label for="tag-input">Tags</label>
					<?php $this->tagField->ShowInput("pure-u-1"); ?>
					<?php $this->tagField->ShowField(); ?>
					<br>
				</fieldset>

				<button form="media-edit" tabindex="4" type="submit" name="modify" class="pure-button pure-button-primary"><?php if($this->modify) echo "Apply"; else echo "Create";?></button>
				<?php if($this->modify): ?>
				<a class="pure-button button-delete" tabindex="5" data-title="<?php echo $this->mediaItem->title; ?>" href="<?php echo ROOT."/api/v2/media/" .$this->mediaItem->media_id; ?>">
					<i class="fa fa-trash"></i>
					Delete
				</a>
				<button tabindex="6" type="submit" name="modify" value="generate-thumbnail" class="pure-button">Generate Thumbnail</button>
				<script type="text/javascript" src="<?php Asset("/resources/js/deleter.js");?>"></script>
				<?php endif; ?>
			</form>
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
