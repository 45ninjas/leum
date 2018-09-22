<?php

/**
 * The tag-field widget.
 */
class Tag_Field implements IWidget
{
	private $tags;
	private $readOnly = false;

	private $slugString;
	private $classText = "";

	private static $templateDone = false;

	function __construct($arguments)
	{
		if(isset($arguments['tags']))
		{
			$this->tags = $arguments['tags'];
			$this->slugString = CreateSlugString($this->tags);
		}

		if(isset($arguments['read only']))
			$this->readOnly = $arguments['read only'];

		if(isset($arguments['class']))
			$this->classText = $arguments['class'];

		if(Leum::Instance() !== null && !$this->readOnly)
		{
			// Is easy auto complete even still used for this?
			// Leum::Instance()->RequireResource('/resources/css/easy-autocomplete.min.css', '<link rel="stylesheet" type="text/css" href="' . GetAsset('/resources/css/easy-autocomplete.min.css') . '">');
			// Leum::Instance()->RequireResource('/resources/js/jquery.easy-autocomplete.min.js', '<script type="text/javascript" src="' . GetAsset('/resources/js/jquery.easy-autocomplete.min.js') . '"></script>');

			Leum::Instance()->RequireResource('/resources/js/leum-tagfield.js', '<script type="text/javascript" src="' . GetAsset('/resources/js/leum-tagfield.js') . '"></script>');
		}
	}

	public function Show()
	{
		?>
		<ul class="tagfield" id="tagfield">
			<input type="hidden" autocomplete="off" id="tags" name="tags" value="<?php echo $this->slugString ?>">
		<?php
		if(isset($this->tags))
		{
			foreach ($this->tags as $tag)
				$this->DoTag($tag);
		}
		?>
		</ul>
		<?php
		if(!$this->readOnly)
			$this->DoInput();

	}

	private function DoInput()
	{
		?><input id="tag-input" class="tag-input <?php echo $this->classText; ?>" type="text" placeholder="Add Tag"><?php
		$this->DoTagTemplate();
	}

	private function DoTag($tag)
	{
		?>
		<li class="leum-tag">
			<input type="hidden" value="<?=$tag?>">
			<span><?=$tag?></span>
			<?php if(!$this->readOnly) { ?>
			<a class="tag-delete"><i class="fa fa-close"></i></a>
			<?php } ?>
		</li>
		<?php
	}
	private function DoTagTemplate()
	{
		// The template only needs to be shown once.
		if(self::$templateDone)
			return;

		self::$templateDone = true;
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
