<?php
class MediaViewer
{
	private $media;
	private $edit;
	private $videoFeatures;
	private $link;
	private $mimeType;
	private $type;
	private $usingAjax;
	private $isFile;

	public function __construct($media, $canEdit = false, $autoPlay = false, $loop = true, $controls = true, $ajax = false)
	{
		if(class_exists('Leum') && Leum::Instance() !== null)
			Leum::Instance()->RequireResource('/resources/css/media-viewer.css', '<link rel="stylesheet" type="text/css" href="' . GetAsset('/resources/css/media-viewer.css') . '">');
		$this->media = $media;
		$this->edit = $canEdit === true;
		$this->media->GetType();
		$this->mimeType = $media->GetMimeType();
		$this->link = $media->GetLink();

		$this->isFile = is_file($media->GetPath());

		$this->videoFeatures = "";

		$this->usingAjax = $ajax == true;
		
		if($autoPlay)
			$this->videoFeatures .= " autoplay";

		if($loop)
			$this->videoFeatures .= " loop";

		if($controls)
			$this->videoFeatures .= " controls";
	}
	public function ShowPreview()
	{ ?>
	<div class="media-viewer preview">
		<?php $this->ShowContent(); ?>
	</div>
	<?php }
	public function ShowFull()
	{ ?>
	<div class="media-viewer">
		<div class="title-bar">
			<span class="title"><?=$this->media->title;?></span>
			<button class="close-button"><i class="fa fa-times"></i></button>
		</div>
		<?php $this->ShowContent(); ?>
	</div>
	<?php }
	public function ShowContent()
	{
		if(!$this->isFile)
		{
			$title = "File Not Found.";
			$desc = "The file could not be found on the system";
			DoMsg($title, $desc);
			return;
		}
		switch ($this->media->type)
		{
			case 'image':
				$this->DoImage();
				break;
			case 'video':
				$this->DoVideo();
				break;
			default:
				$title = "MIME Type Not Supported";
				$desc = "<i>$this->mimeType</i> is not supported by the media viewer.";
				$desc .= "\n<a href=\"$this->link\">View Anyways</a>";
				$this->DoMsg($title, $desc, "", true);
				break;
		}
	}
	private function DoVideo()
	{?>
		<video class="media-item <?=$this->type;?>"<?=$this->videoFeatures; ?> >
			<source src="<?=$this->link;?>" type="<?=$this->mimeType;?>">
			<?php
			$title = "No Video Support.";
			$desc = "It Looks like your browser does not support the $this->mimeType video format";
			$this->DoMsg($title, $desc, "no-support", false);
			?>
		</video>
	<?php }
	
	private function DoImage()
	{?>
		<img class="media-item <?=$this->type;?>" src="<?=$this->link;?>" alt="<?=$this->media->title;?>" />
	<?php }
	private function DoMsg($title, $msg, $class="", $withContainer = true)
	{
		if($withContainer) echo "<div class=\"media-item msg $class\">"; ?>
		<span class="fa-stack fa-3x">
			<i aria-hidden="true" class="fa fa-file-o fa-stack-2x"></i>
			<i aria-hidden="true" class="fa fa-exclamation-triangle fa-stack-1x"></i>
		</span>
		<h3><?=$title;?></h3>
		<p><?=$msg;?></p>
		<?php if($withContainer) echo "</div>"; ?>
	<?php }
}
?>