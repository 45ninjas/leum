<?php
/**
* Default page
*/
require_once SYS_ROOT . "/page-parts/page-buttons.php";
require_once SYS_ROOT . "/page-parts/tag-field.php";
require_once SYS_ROOT . "/core/leum-core.php";
require_once SYS_ROOT . "/page-parts/media-viewer.php";

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

	private $viewer;

	public $useModal = true;
	
	public function __construct($arguments)
	{
		$dbc = Leum::Instance()->GetDatabase();

		if(Leum::Instance() !== null)
		{
			Leum::Instance()->RequireResource('/resources/css/leum-media-viewer.css', '<link rel="stylesheet" type="text/css" href="' . GetAsset('/resources/css/leum-media-viewer.css') . '">');
			Leum::Instance()->RequireResource('/resources/js/media-viewer.js', '<script src="' . GetAsset('/resources/js/media-viewer.js') . '"></script>');
		}

		if(isset($_GET['page']) && is_numeric($_GET['page']))
			$this->pageNum = $_GET['page'] - 1;

		$unwantedTags = null;
		$wantedTags = null;

		if(isset($_GET['q']) && !empty($_GET['q']))
		{
			$query = new QueryReader($_GET['q']);
			$unwantedTags = $query->unwantedTags;
			$wantedTags = $query->wantedTags;
		}

		// Get the items and total items to know how many pages we need.
		$this->itemsToShow = Media::GetWithTags($dbc, $wantedTags, $unwantedTags, $this->pageNum, $this->pageSize);

		$this->totalResults = LeumCore::GetTotalItems($dbc);
		$this->totalPages = ceil($this->totalResults / $this->pageSize);

		$this->pageButtons = new PageButtons($this->totalPages,$this->pageNum + 1);

		Leum::Instance()->RequireResource('tags.js', '<script type="text/javascript" src="' . GetAsset('/resources/js/tags.js') . '"></script>');
	}
	public function Content()
	{ ?>

<div class="main">
	<div class="header">
		<h1>Browse</h1>
	</div>
	<div class="browse-bar">
		<div class="content">
			<!-- <form class="right pure-form" method="GET" action="">
				<?php // $this->tagField->ShowInput(); ?>
				<button class="pure-button pure-button-primary"><i class="fa fa-search"></i></button>
				<?php // $this->tagField->ShowField(); ?>
			</form> -->
			<form class="pure-form" method="GET" action="">
				<input type="text" name="q">
				<button class="pure-button pure-button-primary"><i class="fa fa-search"></i></button>
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

<?php
$this->MediaViewer();
}
function DoItem($mediaItem)
{
	$thumbnailUrl = $mediaItem->GetThumbnail();
	if($thumbnailUrl == null)
		$thumbnailUrl = GetAsset("/resources/graphics/no-thumb.png");

	if($this->useModal)
		$href = "view$mediaItem->media_id";
	else
		$href = ROOT . "/view/$mediaItem->media_id";

	?>
	<a id=<?=$href;?> data-media-index="<?=$mediaItem->media_id;?>" class="item-tile" href="#<?=$href;?>">
		<img src="<?=$thumbnailUrl;?>">
	</a>
	<?php
}
function MediaViewer()
{ ?>
<div id="media-viewer" class="media-viewer full" hidden>
	<h1 id="media-title" class="title"></h1>
	<div class="footer">
		<div class="tag-input">
			<input type="text" id="tag-input-field" placeholder="new tag">
			<ul class="suggestion-box" id="suggestion-box" hidden>
			</ul>
		</div>
		<input id="tag-input" type="hidden" name="tags" value="">
		<div id="tag-editor-field" class="tags tag-field">
		</div>
		<a id="media-edit-link" class="button-stealth" href="#">
			<i class="fa fa-edit"></i>
		</a>
	</div>
</div>
<a id="media-viewer-close" class="viewer-button" hidden>&times;</a>
<a id="media-viewer-next" class="viewer-button" hidden>&rsaquo;</a>
<a id="media-viewer-prev" class="viewer-button" hidden>&lsaquo;</a>
<?php }
}
?>
