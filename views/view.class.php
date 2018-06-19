<?php

class View
{
	function Singular($item, $classString = "")
	{
		echo "<div class=\"$classString\">$item</div>";
	}
	function Multiple($items, $classString = "")
	{
		echo "<div class=\"multiple $classString\">";
		foreach ($items as $item)
		{
			$this->singular($item, $classString);
		}
		echo "</div>";
	}
	function DoTable ($items, $tableClass="", $rowClass="", $emptyStr = "-")
	{
		$columns = array();
		if(isset($items[0]))
		{
			$reflect = new ReflectionClass($items[0]);
			$props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
			foreach ($props as $property)
			{
				$name = $property->name;
				$columns[$name] = $name;
			}
		}
		View::CreateTable($columns, $items, $tableClass, $rowClass, $emptyStr);
	}

	static function CreateTable($columns, $items, $tableClass = "", $rowClass = "", $emptyStr = "-")
	{
		echo "<table class=\"$tableClass\">";

		// Display the table head.
		echo "<thead><tr>";
		foreach ($columns as $variable => $title)
		{
			echo "<th>$title</th>";
		}
		echo "</tr></thead>";

		// Display the table body.
		echo "<tbody>";
		foreach ($items as $item)
		{
			echo "<tr class=\"$rowClass\">";
			foreach ($columns as $variable => $title)
			{
				if(isset($item->$variable))
					$value = $item->$variable;
				else
					$value = "-";
				echo "<td>$value</td>";
			}
			echo "</tr>";
		}
		echo "</tbody>";
		echo "</table>";
	}
}
?>
