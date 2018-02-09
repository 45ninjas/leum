<?php
/**
* Default page
*/
require_once SYS_ROOT . "/core/media.php";
require_once SYS_ROOT . "/core/mapping.php";

require_once 'page-parts/item-preview.php';
require_once 'page-parts/tag-field.php';
class Page
{
	public $title = "Create Media";
	private $mediaItem;
	private $modify = false;
	private $itemPreview;
	private $tagField;

	public function __construct($arguments)
	{
		$dbc = Leum::Instance()->GetDatabase();

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
		}

		if(isset($mediaId))
		{
			$this->mediaItem = Media::GetSingle($dbc, $arguments[0]);
			$this->title = "Edit Media";
		}
		else
			$this->mediaItem = new Media();

		$this->itemPreview = new ItemPreview($this->mediaItem);
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
							<div class="leum-content-container">
								<?php $this->itemPreview->Show(); ?>
							</div>
						</div>

					</div>

					<label for="tag-input">Tags</label>
					<?php $this->tagField->ShowInput("pure-u-1"); ?>
					<?php $this->tagField->ShowField(); ?>
					<br>
				</fieldset>

				<button form="media-edit" tabindex="4" type="submit" name="modify" class="pure-button pure-button-primary"><?php if($this->modify) echo "Apply"; else echo "Create";?></button>
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
