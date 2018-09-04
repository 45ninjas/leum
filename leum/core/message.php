<?php

class Message
{
	public static $messages = array();
	public static function Create($class, $text, $location = "default")
	{
		$msg = new Message($class, $text);
		$msg->AddMessage($location);
	}
	public static function ShowMessages($location = "default", $class = "")
	{
		if(isset(self::$messages[$location]))
		{
			echo "<div class=\"messages $location $class\">";
			foreach (self::$messages[$location] as $message)
			{
				echo "<div class=\"msg $message->class\">$message->text</div>";
			}
			echo "</div>";
		}
	}
	private function AddMessage($location = "default")
	{
		self::$messages[$location][] = $this;
	}
	public $class;
	public $text;
	public function __construct($class, $text)
	{
		$this->class = $class;
		$this->text = $text;
	}
}

?>
