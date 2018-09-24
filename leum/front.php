<?php
/**
 * Front End
 */
class Front
{
	static function GetWidget($name, $arguments = null)
	{
		try
		{
			// Find if a widget exists in the widgets directory.
			$widgetFile = SYS_ROOT . "/leum/widgets/$name.php";

			if(!is_file($widgetFile))
				throw new Exception("widget file $name.php does not exist.");

			// Include the widget.
			include_once $widgetFile;

			// Complain if it the widget name is also a class.
			if(!class_exists($name))
				throw new Exception("no widget class found with the name of $name");

			// Actually create the widget class.
			$widget = new $name($arguments);
			return $widget;
		}
		catch(Exception $e)
		{
			Message::Create("warning", "Unable to load the '$name' widget. Reason: " . $e->GetMessage());
		}
		return null;
	}
}

interface IWidget
{
	function __construct($arguments);
	public function Show();
}
?>