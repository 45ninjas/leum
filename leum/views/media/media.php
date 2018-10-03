<?php
namespace Views;
class Media
{
	public $size = THUMB_SIZE;
	public $itemClass = "media-item";
	public $listClass = "media-list";
	public $useModal = true;

	public function __construct($args = null)
	{
		if(is_array($args))
		{
			// Set the use modal.
			if(isset($args['use modal']) && $args['use modal'] === false)
				$this->useModal == false;
			else
				$this->useModal == true;

			// Set the size
			if(isset($args['size']))
				$this->size = $args['size'];

			// Set the item and list classes.
			if(isset($args['item class']))
				$this->itemClass = $args['item class'];

			if(isset($args['list class']))
				$this->listClass = $args['list class'];
		}

		// Get the instance of leum so we can require a resource.
		
		if($this->useModal)
		{
			$leum = \Leum::Instance();
			$leum->RequireResource('media-viewer.js', "<script src=\"" . \GetAsset('/resources/js/media-viewer.js') . "\"></script>");

			\LeumCore::AddHook('leum.front.footer', function()
			{
				// Add the modal markup to the footer.
			?>
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
			<?php
			});
		}
	}

	public function List($mediaItems, $details = false)
	{
		// Add details or list to the class string.
		if($details)
			$this->listClass .= " details";
		else
			$this->listClass .= " thumbnails";

		echo "<div class=\"$this->listClass\">" . PHP_EOL;

		if($details)
		{
			foreach ($mediaItems as $item)
				$this->Details($item);
		}
		else
		{
			foreach ($mediaItems as $item)
				$this->Thumbnail($item);
		}

		echo '</div>' . PHP_EOL;
	}
	
	public function Thumbnail($mediaItem)
	{
		$thumbnail = $mediaItem->GetThumbnail();
		if($thumbnail == null)
			$thumbnail = \GetAsset("/resources/graphics/no-thumb.png");

		if($this->useModal)
			$href = "#view$mediaItem->id";
		else
			$href = ROOT . "/view/$mediaItem->id";

		?>
		<a id="<?=$href;?>" data-media-index="<?=$mediaItem->id;?>" class="<?=$this->itemClass?>" href="<?=$href?>">
			<img src="<?=$thumbnail?>">
		</a>
		<?php
	}
	
	public function Details($mediaItem)
	{

	}
}

?>
