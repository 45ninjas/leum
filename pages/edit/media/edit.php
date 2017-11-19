<?php
/**
* Default page
*/
require_once 'api/v1/media.php';
class Page
{
	public $title = "Create Media";
	private $mediaItem;
	private $modify = false;
	private $db;

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
					<div class="pure-g">
						<div class="pure-u-1 pure-u-md-1-2">
							<label for="title">Title</label>
							<input tabindex="1" class="pure-u-1" type="text" name="title" id="title" placeholder="Title" <?php $this->EchoValue($this->mediaItem->title); ?>>
						</div>
						<div class="pure-u-1 pure-u-md-1-2">
							<label for="path">Path</label>
							<input tabindex="2" class="pure-u-1" type="text" name="path" id="path" placeholder="Path" <?php $this->EchoValue($this->mediaItem->path); ?>>
						</div>
						<div class="pure-u-1 pure-u-md-1">
							<label for="source">Source</label>
							<input tabindex="3" class="pure-u-1" type="text" name="source" id="source" placeholder="Source of Media" <?php $this->EchoValue($this->mediaItem->source); ?>>
						</div>
					</div>
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
