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
		if(isset($_GET['tags']) && !empty($_GET['tags']))
		{
			$queryString = strtolower($_GET['tags']);

			$queryString = preg_replace("[^A-Za-z0-9-]", "", $queryString);
			$tagSlugs = ParseSlugString($queryString);
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
	<div class="browse-bar">
		<div class="content">
			<form class="right pure-form" method="GET" action="">
				<?php $this->tagField->ShowInput(); ?>
				<button class="pure-button pure-button-primary"><i class="fa fa-search"></i></button>
				<?php $this->tagField->ShowField(); ?>
			</form>
		</div>
	</div>
	<div class="content">
		<?php echo "<p>$this->totalResults results, $this->totalPages pages.</p>" ?>
	</div>

		<div class="items">
			<?php foreach ($this->itemsToShow as $item)
			{
				$this->DoItem($item);	
			} ?>
		</div>
	<div class="content">
		<?php if($this->totalPages > 1) $this->pageButtons->DoButtons(); ?>
	</div>
</div>

<?php }
function DoItem($mediaItem)
{
	$thumbnailUrl = $mediaItem->GetThumbnail();
	?>
	<a class="item-tile" href="<?php echo ROOT . "/view/" . $mediaItem->media_id; ?>">
		<img src="<?php echo $thumbnailUrl ?>">
	</a>
	<?php
}
}
?>
