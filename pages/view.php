<?php
/**
* View individual pages.
*/
require_once 'api/v1/media.php';
class Page
{
	public $title = "View";
	private $mediaItem;
	private $db;

	public function __construct($arguments)
	{
		$this->db = Leum::Instance()->GetDatabase();

		$mediaId = null;
		if(isset($arguments[0]) && is_numeric($arguments[0]))
		{
			$mediaId = $arguments[0];
		
			$this->mediaItem = Media::Get($this->db, $arguments[0]);
			$this->title = $this->mediaItem->title;
		}
	}
	public function Content()
	{ ?>

	<div class="main">
		<div class="viewer-content">
			<img src="<?php echo ROOT.MEDIA_DIR.$this->mediaItem->path; ?>">
			<video autoplay="yes" loop="yes" controls="yes">
				<source src="<?php echo ROOT.MEDIA_DIR.$this->mediaItem->path; ?>">
			</video>
		</div>
		<div class="content viewer-meta">
			<h3><?php echo $this->mediaItem->title; ?></h3>
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
