<?php
/**
* Default page
*/
require_once SYS_ROOT . "/leum/page-parts/page-buttons.php";
require_once SYS_ROOT . "/leum/page-parts/tag-field.php";
require_once SYS_ROOT . "/leum/core/leum-core.php";
require_once SYS_ROOT . "/leum/page-parts/media-viewer.php";

require_once SYS_ROOT . "/leum/views/media/media.php";

class browse implements IPage
{
	private $itemsToShow;

	// Pagination!
	public $pageNum = 0;
	public $pageSize = PAGE_SIZE;

	private $totalResults;
	private $totalPages;

	// Tags
	private $wantedTags;
	private $unwantedTags;

	// Widgets and Views
	private $mediaView;
	private $header;
	private $pageButtons;
	private $tagField;
	private $viewer;
	
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		// Require the tags script.
		$leum->RequireResource('tags.js', '<script type="text/javascript" src="' . GetAsset('/resources/js/tags.js') . '"></script>');

		// Get the parameters like Page, tags and filters.
		if(isset($_GET['page']) && is_numeric($_GET['page']))
			$this->pageNum = $_GET['page'] - 1;

		// WantedTags/IncludeTags.
		$this->wantedTags = null;
		if(isset($_GET['t']) && !empty($_GET['t']))
			$this->wantedTags = explode(',', $_GET['t']);

		// Un-WantedTags/ExcludeTags.
		$this->unwantedTags = null;
		if(isset($_GET['et']) && !empty($_GET['et']))
			$this->unwantedTags = explode(',', $_GET['et']);

		// Get the media using the MediaQuery builder.
		$query = new MediaQuery($dbc);
		$query->Order('date','desc');
		$query->Fields(['media.*']);
		$query->Pages($this->pageNum, $this->pageSize);

		// Filter using tags if they exist.
		if(isset($this->wantedTags))
			$query->Tags($this->wantedTags);

		$this->itemsToShow = $query->Execute();

		// Pagination!
		$this->totalResults = LeumCore::GetTotalItems($dbc);
		$this->totalPages = ceil($this->totalResults / $this->pageSize);

		$this->pageButtons = new PageButtons($this->totalPages,$this->pageNum + 1);

		$this->mediaView = new \Views\Media();

		$this->header = Front::GetWidget('page_header',
		[
			'title'=>'Browse',
			'content' => [$this, 'FilterBox']
		]);
		$leum->SetTitle("Browse");
	}
	public function Content()
	{ ?>

<div class="main">
	<?php $this->header->Show(); ?>
	<div class="content">
		<p><?="$this->totalResults results, $this->totalPages pages."?></p>
	</div>
	<?php $this->mediaView->List($this->itemsToShow, false); ?>
	<div class="content">
		<?php if($this->totalPages > 1) $this->pageButtons->DoButtons(); ?>
	</div>
</div>

<?php
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
