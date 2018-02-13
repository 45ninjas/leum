<?php
/**
* View individual pages.
*/
require_once SYS_ROOT . '/core/leum-core.php';
require_once SYS_ROOT . '/page-parts/media-viewer.php';
require_once SYS_ROOT . '/page-parts/tag-field.php';
class Page
{
	public $title = "View";
	private $mediaItem;
	private $viewer;
	private $tagField;

	public function __construct($arguments)
	{
		$dbc = Leum::Instance()->GetDatabase();

		$mediaId = null;
		if(isset($arguments[0]) && is_numeric($arguments[0]))
		{
			$mediaId = $arguments[0];
		
			$this->mediaItem = Media::GetSingle($dbc, $arguments[0]);
			$this->title = $this->mediaItem->title;
		}
		$this->viewer = new MediaViewer($this->mediaItem, true, true, true, true, false);
		$this->tagField = new TagField($this->mediaItem->GetTags(), true);
	}
	public function Content()
	{ ?>

	<div class="main">
		<div class="leum-content-container">
			<?php $this->viewer->ShowFull(); ?>
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
