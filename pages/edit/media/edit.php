<?php
/**
* Default page
*/
require_once 'api/v1/media.php';
require_once 'page-parts/item-preview.php';
class Page
{
	public $title = "Create Media";
	private $mediaItem;
	private $modify = false;
	private $db;
	private $itemPreview;

	public function __construct($arguments)
	{
		$this->db = Leum::Instance()->GetDatabase();

		$mediaId = null;
		if(isset($arguments[0]) && is_numeric($arguments[0]))
		{
			$this->modify = true;
			$mediaId = $arguments[0];
		}

		if(isset($_POST['modify']) && isset($_POST['title']) && isset($_POST['path']) && isset($_POST['source']))
		{
			$this->mediaItem = new Media();
			$this->mediaItem->title = $_POST['title'];
			$this->mediaItem->path = $_POST['path'];
			$this->mediaItem->source = $_POST['source'];
			Media::Insert($this->db, $this->mediaItem,$mediaId);
		}

		if(isset($mediaId))
		{
			$this->mediaItem = Media::Get($this->db, $arguments[0]);
			$this->title = "Edit Media";
		}
		else
			$this->mediaItem = new Media();

		$this->itemPreview = new ItemPreview($this->mediaItem);
	}
	public function Content()
	{ ?>

	<div class="main">
		<div class="header">
			<h1><?php echo $this->title; ?></h1>
		</div>
		<div class="content">
			<form class="pure-form pure-form-stacked" method="post">
				<fieldset>
					<legend>Media Information</legend>
					<div class="pure-g">
						<div class="pure-u-1 pure-u-md-1-2">
							<label for="title">Title</label>
							<input class="pure-u-1" tabindex="1" type="text" name="title" id="title" placeholder="Title" <?php $this->EchoValue($this->mediaItem->title); ?>>

							<label for="path">Path</label>
							<input class="pure-u-1" tabindex="2" type="text" name="path" id="path" placeholder="Path" <?php $this->EchoValue($this->mediaItem->path); ?>>

							<label for="source">Source</label>
							<textarea class="pure-u-1 leum-textarea" tabindex="3" name="source" id="source" placeholder="Source of Media item"><?php if($this->modify) echo $this->mediaItem->source; ?></textarea>
						</div>
						<div class="pure-u-1 pure-u-md-1-2">
							<div class="leum-content-container">
								<?php $this->itemPreview->Show(); ?>
							</div>
						</div>
					</div>
				</fieldset>
				<fieldset>
					<legend>Tags</legend>

				</fieldset>
				<button tabindex="4" type="submit" name="modify" class="pure-button pure-button-primary"><?php if($this->modify) echo "Apply"; else echo "Create";?></button>
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
