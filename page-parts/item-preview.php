<?php
class ItemPreview
{
	// Get the MIME type of the file and provide the correct html.

	public $mediaItem;
	private $type = "undefined";
	private $baseType;
	private $isFile = false;
	function __construct($mediaItem)
	{
		$this->mediaItem = $mediaItem;
		$file = $this->mediaItem->GetPath();
		if(is_file($file))
		{
			$this->isFile = true;
			$this->type = mime_content_type($file);
			$this->baseType = explode('/', $this->type)[0];
		}
	}

	public function Show()
	{
		if(!$this->isFile)
		{
			$this->DoNoFile();
			return;
		}

		switch ($this->baseType)
		{
			case 'video':
				$this->DoVideo();		
				break;

			case 'image':
				$this->DoImage();
				break;
			
			default:
				$this->DoUnknown();
				break;
		}
	}

	private function DoVideo()
	{
		?>
		<video class="leum-content-item" controls src="<?php echo $this->mediaItem->GetLink(); ?>">
			<div class="leum-bad-type">
				<span class="fa-stack fa-3x">
					<i aria-hidden="true" class="fa fa-file-o fa-stack-2x"></i>
					<i aria-hidden="true" class="fa fa-exclamation-triangle fa-stack-1x"></i>
				</span>
				<h3>No Video Support.</h3>
				<p>Looks like you are using a browser that does not support this video format.</p>
			</div>
		</video>
		<?php
	}
	private function DoImage()
	{
		?>
		<img class="leum-content-item" src="<?php echo $this->mediaItem->GetLink(); ?>">
		<?php
	}
	private function DoUnknown()
	{
		?>
		<div class="leum-content-item leum-bad-type">
			<span class="fa-stack fa-3x">
				<i aria-hidden="true" class="fa fa-file-o fa-stack-2x"></i>
				<i aria-hidden="true" class="fa fa-exclamation-triangle fa-stack-1x"></i>
			</span>
			<h3>MIME Type Not Supported.</h3>
			<p>'<i><?php echo $this->type; ?></i>' not supported in preview.<br>
			<a href="<?php echo $this->mediaItem->GetLink(); ?>">View Anyways.</a></p>
		</div>
		<?php
	}
	private function DoNoFile()
	{
		?>
		<div class="leum-content-item leum-bad-type">
			<span class="fa-stack fa-3x">
				<i aria-hidden="true" class="fa fa-file-o fa-stack-2x"></i>
				<i aria-hidden="true" class="fa fa-exclamation-triangle fa-stack-1x"></i>
			</span>
			<h3>File Not Found.</h3>
			<p>The file could not be found on the system.</p>
		</div>
		<?php
	}

}
?>