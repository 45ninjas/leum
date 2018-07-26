<?php
class GitStatus
{
	public $date;
	public $hash;
	public $message;
	public $valid = false;
	function __construct($inMessages = false)
	{
		exec("git log --pretty=%s,%cd,%h --date=relative -n1 HEAD", $output, $return);

		if($return)
			return;

		$gitlog = explode(',', $output[0]);

		$this->hash = array_pop($gitlog);
		$this->date = array_pop($gitlog);
		$this->message = array_pop($gitlog);

		$this->valid = true;

		if($inMessages)
			Message::Create('msg-green', "Git Status: " . $this->GetMessage());
	}
	
	public function GetMessage()
	{
		return "$this->hash, last updated $this->date.<br>$this->message";
	}
}
?>