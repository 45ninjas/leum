<?php
/**
* Default page
*/
require_once SYS_ROOT . "/page-parts/page-buttons.php";
require_once SYS_ROOT . "/page-parts/tag-field.php";
require_once SYS_ROOT . "/core/leum-core.php";
require_once SYS_ROOT . "/page-parts/media-viewer.php";

class browse implements IPage
{
	public $pageNum = 0;
	public $pageSize = 50;

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
		$leum->RequireResource('/resources/css/leum-media-viewer.css', '<link rel="stylesheet" type="text/css" href="' . GetAsset('/resources/css/leum-media-viewer.css') . '">');
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
		<h1>Browse</h1>
		<div class="pure-menu pure-menu-horizontal">
			<ul class="pure-menu">
				<!-- <li class="pure-menu-item"><a class="pure-menu-link">Filter <i class="fa fa-filter"></i></a></li> -->
			</ul>
		</div>
	</div>
	<div class="content">
		<?php $this->FilterBox(); ?>
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
			<input id="tag-input" type="hidden" name="tags" value="">
			<input type="text" id="tag-input-field" placeholder="new tag">
			<ul class="suggestion-box" id="suggestion-box" hidden>
			</ul>
		</div>
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
		<label for ="contain-tag-input">Include Tags</label>
		<div class="contain-tags">
			<div class="tag-input">
				<input id="contain-tags" type="hidden" name="t" value="<?=$wantedText?>">
				<input class="pure-u-1" type="text" id="contain-tag-input" placeholder="tag" autocomplete="off">
				<!--  -->
				<ul class="suggestion-box" id="contain-suggestion-box" hidden>
				</ul>
			</div>
			<div id="contain-tag-field" class="tags tag-field">
			</div>
		</div>
		<!-- <label for ="contain-tag-input">Exclude Tags</label>
		<div class="exclude-tags">
			<div class="tag-input">
				<input id="exclude-tags" type="hidden" name="et" value="<?=$unwantedText?>">
				<input class="pure-u-1" type="text" id="exclude-tag-input" placeholder="tag" disabled="" autocomplete="off">
				<br>
				<ul class="suggestion-box" id="exclude-suggestion-box" hidden>
				</ul>
			</div>
			<div id="exclude-tag-field" class="tags tag-field">
			</div>
		</div> -->
		<button type="submit" class="pure-button pure-button-primary">Apply</button>
		<button type="submit" class="pure-button" id="tag-filter-clear">Clear</button>
	</form>
</div>
<script type="text/javascript">
	window.onload = function()
	{
		var Tags = document.querySelector("#tag-filter #contain-tags");
		var Input = document.querySelector("#tag-filter #contain-tag-input");
		var Field = document.querySelector("#tag-filter #contain-tag-field");
		var SuggestionBox = document.querySelector("#tag-filter #contain-suggestion-box");

		containEditor = new TagEditor(Input, Field,Tags, SuggestionBox);
		containEditor.SetTags(Tags.value.split(','));
		containEditor.resultLimit = 10;

		Tags = document.querySelector("#tag-filter #exclude-tags");
		Input = document.querySelector("#tag-filter #exclude-tag-input");
		Field = document.querySelector("#tag-filter #exclude-tag-field");
		SuggestionBox = document.querySelector("#tag-filter #exclude-suggestion-box");

		excludeEditor = new TagEditor(Input, Field,Tags, SuggestionBox);
		excludeEditor.SetTags(Tags.value.split(','));
		containEditor.resultLimit = 10;

		var clearButton = document.querySelector("#tag-filter #tag-filter-clear");
		clearButton.onclick = function()
		{
			excludeEditor.Clear();
			containEditor.Clear();
		};
	}
</script>
<?php
}
}
?>
