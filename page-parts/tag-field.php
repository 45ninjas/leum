<?php
class TagField
{
	private $tags;
	private $templateDone = false;
	function __construct($tags)
	{
		$this->tags = $tags;
		Leum::Instance()->RequireResource('resources/css/leum-tagfield.css', '<link rel="stylesheet" type="text/css" href="' . GetAsset('resources/css/leum-tagfield.css') . '">');
		Leum::Instance()->RequireResource('resources/js/leum-tagfield.js', '<script type="text/javascript" src="' . GetAsset('resources/js/leum-tagfield.js') . '"></script>');

		Leum::Instance()->RequireResource('resources/css/easy-autocomplete.min.css', '<link rel="stylesheet" type="text/css" href="' . GetAsset('resources/css/easy-autocomplete.min.css') . '">');
		Leum::Instance()->RequireResource('resources/js/jquery.easy-autocomplete.min.js', '<script type="text/javascript" src="' . GetAsset('resources/js/jquery.easy-autocomplete.min.js') . '"></script>');
	}

	public function ShowField()
	{
		?>
		<ul class="tagfield" id="tagfield">
		<?php
		foreach ($this->tags as $tag)
		{
			$this->ShowTag($tag);
		}
		?>
		</ul>
		<?php
	}
	public function ShowInput($classText = "")
	{
		?><input id="tag-input" class="tag-input <?php echo $classText; ?>" type="text" list="known-tags" placeholder="Add Tag"><?php
		$this->TagTemplate();
	}
	public function ShowTag($tag)
	{
		?>
		<li class="leum-tag">
			<input type="hidden" name="tags[]" value="<?php echo $tag->tag_id; ?>">
			<span><?php echo $tag->title; ?></span>
			<a class="tag-delete"><i class="fa fa-close"></i></a>
		</li>
		<?php
	}
	private function TagTemplate()
	{
		// The template only needs to be shown one.
		if($this->templateDone)
			return;

		$this->templateDone = true;
		?>
		<template id="tag-template">
			<li class="leum-tag">
				<input type="hidden" name="tags[]" value="">
				<span></span>
				<a class="tag-delete" href=""><i class="fa fa-close"></i></a>
			</li>
		</template>
		<?php
	}
}
?>
