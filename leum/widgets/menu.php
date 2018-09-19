<?php

/**
 * The menu widget
 */
class Menu implements IWidget
{
	public $class = "";
	public $items;
	function __construct($arguments)
	{
		if(isset($arguments['class']))
			$this->class = $arguments['class'];

		if(isset($arguments['items']))
			$this->items = $arguments['items'];
	}

	public function Show()
	{
		echo "<div class=\"menu$this->class\">";
		foreach ($this->items as $item)
		{
			if(isset($item['class']))
				$class = ' ' . $item['class'];
			else
				$class = '';

			$href = $item['href'];
			$content = $item['content'];

			echo "<a href=\"$href\" class=\"menu-item$class\">$content</a>";
		}
		echo "</div>";
	}
}

?>
