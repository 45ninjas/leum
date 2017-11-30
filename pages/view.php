<?php
/**
* View individual pages.
*/
require_once 'api/v1/media.php';
require_once 'page-parts/item-preview.php';
require_once 'page-parts/tag-field.php';
class Page
{
	public $title = "View";
	private $mediaItem;
	private $itemPreview;
	private $tagField;

	public function __construct($arguments)
	{
		$dbc = Leum::Instance()->GetDatabase();

		$mediaId = null;
		if(isset($arguments[0]) && is_numeric($arguments[0]))
		{
			$mediaId = $arguments[0];
		
			$this->mediaItem = Media::Get($dbc, $arguments[0]);
			$this->title = $this->mediaItem->title;
		}
		$this->itemPreview = new ItemPreview($this->mediaItem);
		$this->tagField = new TagField($this->mediaItem->GetTags(), true);
	}
	public function Content()
	{ ?>

	<div class="main">
		<div class="leum-content-container">
			<?php $this->itemPreview->Show(); ?>
		</div>
		<div class="content viewer-meta">
			<h3><?php echo $this->mediaItem->title; ?></h3>
			<?php $this->tagField->ShowField(); ?>
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
