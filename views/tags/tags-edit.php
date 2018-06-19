<?php
require_once SYS_ROOT . "/views/view.class.php";

class TagView extends View
{
	function Singular ($item, $classString = "")
	{
		echo "<div class=\"tag $classString\">$item->tag_id, $item->slug, $item->count</div>";
	}
	function DoTable ($items, $tableClass="", $rowClass="", $emptyStr = "-")
	{
		$columns = array
		(
			"tag_id"	=> "ID",
			"slug"		=> "Slug",
			"count"		=> "Total Uses"
		);
		View::CreateTable($columns, $items, $tableClass, $rowClass, $emptyStr);
	}
}
?>