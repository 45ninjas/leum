<?php 
/**
* Base View
*/
Interface Viewable
{
	public function TheTitle();
	public function __construct($arguments);
	public function TheContent();
	public function TheHead();
}

?>
