<?php
/**
* Default page
*/
require_once "page-parts/page-buttons.php";
require_once "page-parts/tag-field.php";
require_once "api/v1/leum.api.php";
class Page
{
	public $title = "Browse";
	
	public $pageNum = 0;
	public $pageSize = 50;

	private $pageButtons;
	private $itemsToShow;

	private $totalResults;
	private $totalPages;

	private $tagField;
	
	public function __construct($arguments)
	{
		$dbc = Leum::Instance()->GetDatabase();

		// Get the page number.
		if(isset($_GET['page']) && is_numeric($_GET['page']))
			$this->pageNum = $_GET['page'] - 1;

		// Get the tags to search for.
		$tags = array();
		if(isset($_GET['tags']))
		{
			$queryString = strtolower($_GET['tags']);

			$queryString = preg_replace("[^A-Za-z0-9-]", "", $queryString);
			$tagSlugs = explode(' ', $queryString);
			$tagSlugs = array_filter($tagSlugs);
			$tags = Browse::GetTagsFromSlugs($dbc, $tagSlugs);
		}

		// Get the items and total items to know how many pages we need.
		$this->itemsToShow = Browse::GetItems($dbc, $tags, $this->pageNum, $this->pageSize);

		$this->totalResults = Browse::GetTotalItems($dbc);
		$this->totalPages = ceil($this->totalResults / $this->pageSize);

		$this->pageButtons = new PageButtons($this->totalPages,$this->pageNum + 1);

		$this->tagField = new TagField($tags, false);
	}
	public function Content()
	{ ?>

<div class="main">
	<div class="header">
		<h1>Browse</h1>
	</div>
	<div class="content">
		<?php $this->tagField->ShowField(); ?>
		<?php echo "$this->totalResults results, $this->totalPages pages." ?>
	</div>
		<div class="items">
			<?php foreach ($this->itemsToShow as $item)
			{
				$this->DoItem($item);	
			} ?>
			<!-- <span class="blank"></span> -->
		</div>
	<div class="content">
		<?php if($this->totalPages > 1) $this->pageButtons->DoButtons(); ?>
	</div>
</div>

<?php }
function DoItem($mediItem)
{
	$thumbnailUrl = $mediItem->GetThumbnail();
	?>
	<a class="item-tile" src="">
		<img src="<?php echo $thumbnailUrl ?>">
	</a>
	<?php
}
}
?>
