<?php
/**
* Default page
*/
require_once "page-parts/page-buttons.php";
class Page
{
	public $title = "Browse";
	private $pageNum = 0;
	private $pageButtons;
	public function __construct($arguments)
	{
		if(isset($_GET['page']) && is_numeric($_GET['page']))
		{
			$this->pageNum = $_GET['page'] - 1;
		}
		$this->pageButtons = new PageButtons(20,$this->pageNum + 1);
	}
	public function Content()
	{ ?>

<div class="main">
	<div class="header">
		<h1>Browse</h1>
	</div>
	<div class="content">
		<p>This is where all the media items on page <?php echo $this->pageNum +1; ?> will be listed.</p>
		<?php $this->pageButtons->DoButtons(); ?>
	</div>
</div>

<?php }
function DoThumbnail($mediItem)
{
	?>
	
	<?php
}
}
?>
