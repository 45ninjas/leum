<?php
/**
* Default page
*/
require_once SYS_ROOT . "/leum/page-parts/page-buttons.php";
require_once SYS_ROOT . "/leum/page-parts/tag-field.php";
require_once SYS_ROOT . "/leum/core/leum-core.php";
require_once SYS_ROOT . "/leum/page-parts/media-viewer.php";

class browse implements IPage
{
	public $pageNum = 0;
	public $pageSize = PAGE_SIZE;

	private $pageButtons;
	private $itemsToShow;

	private $totalResults;
	private $totalPages;

	private $tagField;

	private $viewer;

	private $wantedTags;
	private $unwantedTags;

	public $useModal = true;
	
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		// Require the resources. 
		$leum->RequireResource('/resources/js/media-viewer.js', '<script src="' . GetAsset('/resources/js/media-viewer.js') . '"></script>');
		$leum->RequireResource('tags.js', '<script type="text/javascript" src="' . GetAsset('/resources/js/tags.js') . '"></script>');

		$leum->SetTitle("Browse");


		// Get the page number.
		if(isset($_GET['page']) && is_numeric($_GET['page']))
			$this->pageNum = $_GET['page'] - 1;

		// Get the wantedTags/IncludeTags.
		$this->wantedTags = null;
		if(isset($_GET['t']) && !empty($_GET['t']))
			$this->wantedTags = explode(',', $_GET['t']);

		// Get the unwantedTags/ExcludeTags.
		$this->unwantedTags = null;
		if(isset($_GET['et']) && !empty($_GET['et']))
			$this->unwantedTags = explode(',', $_GET['et']);

		// Get media items based on the unwanted tags, wanted tags and page number.
		$this->itemsToShow = Media::GetWithTags($dbc, $this->wantedTags, $this->unwantedTags, $this->pageNum, $this->pageSize);

		$this->totalResults = LeumCore::GetTotalItems($dbc);
		$this->totalPages = ceil($this->totalResults / $this->pageSize);

		$this->pageButtons = new PageButtons($this->totalPages,$this->pageNum + 1);

	}
	public function Content()
	{ ?>

<div class="main">
	<div class="header">
		<div class="content">
			<h1>Browse</h1>
			<div class="pure-menu pure-menu-horizontal">
				<ul class="pure-menu">
					<!-- <li class="pure-menu-item"><a class="pure-menu-link">Filter <i class="fa fa-filter"></i></a></li> -->
				</ul>
			</div>
			<?php $this->FilterBox(); ?>
		</div>
	</div>
	<div class="content">
		<p><?="$this->totalResults results, $this->totalPages pages."?></p>
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
		$href = "view$mediaItem->id";
	else
		$href = ROOT . "/view/$mediaItem->id";

	?>
	<a id=<?=$href;?> data-media-index="<?=$mediaItem->id;?>" class="item-tile" href="#<?=$href;?>">
		<img src="<?=$thumbnailUrl;?>">
	</a>
	<?php
}
function MediaViewer()
{ ?>
<div id="media-viewer" class="media-viewer full" hidden>
	<div class="media-viewer-modal">
		<a id="media-viewer-next" class="viewer-button" hidden>&rsaquo;</a>
		<a id="media-viewer-prev" class="viewer-button" hidden>&lsaquo;</a>
		<div class="header">
			<span id="media-title"></span>
			<button class="close-button" id="media-viewer-close"><i class="fa fa-times"></i></button>
		</div>
		<div class="content">
		</div>
		<div class="footer">
			<a id="media-edit-link" class="edit-button" href="#">
				<i class="fa fa-edit"></i>
			</a>
			<div class="tag-input">
				<input id="tag-input" type="hidden" name="tags" value="">
				<input type="text" id="tag-input-field" placeholder="new tag">
				<ul class="suggestion-box" id="suggestion-box" hidden>
				</ul>
			</div>
			<div id="tag-editor-field" class="tags tag-field">
			</div>
		</div>
	</div>
</div>
<?php }
function FilterBox()
{
$wantedText = "";
$unwantedText = "";

if(isset($this->unwantedTags))
	$unwantedText = implode(',', $this->unwantedTags);

if(isset($this->wantedTags))
	$wantedText = implode(',', $this->wantedTags);
?>
<div id="tag-filter" class="tag-filter">
	<form class="pure-form" method="GET">
		<div class="contain-tags">
			<div class="tag-input">
				<input id="contain-tags" type="hidden" name="t" value="<?=$wantedText?>">
				<div class="search-box">
					<input tabindex="1" class="pure-u-1" type="text" id="contain-tag-input" placeholder="Search" autocomplete="off">
					<button class="pure-button" id="tag-filter-clear"><i class="fa fa-times"></i></button>
					<button tabindex="2" type="submit" class="pure-button pure-button-primary"><i class="fa fa-search"></i></button>
				</div>
				<ul class="suggestion-box" id="contain-suggestion-box" hidden>
				</ul>
			</div>
			<div id="contain-tag-field" class="tags tag-field">
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", load);

	function load() {
		var Tags = document.querySelector("#tag-filter #contain-tags");
		var Input = document.querySelector("#tag-filter #contain-tag-input");
		var Field = document.querySelector("#tag-filter #contain-tag-field");
		var SuggestionBox = document.querySelector("#tag-filter #contain-suggestion-box");

		containEditor = new TagEditor(Input, Field,Tags, SuggestionBox);
		containEditor.SetTags(Tags.value.split(','));
		containEditor.resultLimit = 10;

		var clearButton = document.querySelector("#tag-filter #tag-filter-clear");
		clearButton.onclick = function()
		{
			containEditor.Clear();
		};
	}
</script>
<?php
}
}
?>
