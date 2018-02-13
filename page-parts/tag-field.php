<?php
class TagField
{
	private $tags;
	private $templateDone = false;
	private $readOnly = false;

	private $slugString;

	function __construct($tags, $readOnly = false)
	{
		$this->tags = $tags;
		$this->slugString = CreateSlugString($tags);
		
		$this->readOnly = $readOnly;

		if(Leum::Instance() !== null)
		{
			Leum::Instance()->RequireResource('/resources/css/leum-tagfield.css', '<link rel="stylesheet" type="text/css" href="' . GetAsset('/resources/css/leum-tagfield.css') . '">');
			if(!$readOnly)
			{
				Leum::Instance()->RequireResource('/resources/js/leum-tagfield.js', '<script type="text/javascript" src="' . GetAsset('/resources/js/leum-tagfield.js') . '"></script>');
				Leum::Instance()->RequireResource('/resources/css/easy-autocomplete.min.css', '<link rel="stylesheet" type="text/css" href="' . GetAsset('/resources/css/easy-autocomplete.min.css') . '">');
				Leum::Instance()->RequireResource('/resources/js/jquery.easy-autocomplete.min.js', '<script type="text/javascript" src="' . GetAsset('/resources/js/jquery.easy-autocomplete.min.js') . '"></script>');
			}
		}
	}

	public function ShowField()
	{
		?>
		<ul class="tagfield" id="tagfield">
			<input type="hidden" autocomplete="off" id="tags" name="tags" value="<?php echo $this->slugString ?>">
		<?php
		if(isset($this->tags))
		{
			foreach ($this->tags as $tag)
				$this->ShowTag($tag);
		}
		?>
		</ul>
		<?php
	}
	public function ShowInput($classText = "")
	{
		if($this->readOnly)
			throw new Exception("Cannot edit this tagfield. This tagfield is in read-only mode", 1);
			
		?><input id="tag-input" class="tag-input <?php echo $classText; ?>" type="text" placeholder="Add Tag"><?php
		$this->TagTemplate();
	}
	private function ShowTag($tag)
	{
		?>
		<li class="leum-tag">
			<input type="hidden" value="<?php echo $tag->slug; ?>">
			<span><?php echo $tag->title; ?></span>
			<?php if(!$this->readOnly) { ?>
			<a class="tag-delete"><i class="fa fa-close"></i></a>
			<?php } ?>
		</li>
		<?php
	}
	private function TagTemplate()
	{
		// The template only needs to be shown once.
		if($this->templateDone)
			return;

		$this->templateDone = true;
		?>
		<template id="tag-template">
			<li class="leum-tag">
				<input type="hidden" value="">
				<span></span>
				<?php if(!$this->readOnly) { ?>
				<a class="tag-delete"><i class="fa fa-close"></i></a>
				<?php } ?>
			</li>
		</template>
		<?php
	}
}
?>
