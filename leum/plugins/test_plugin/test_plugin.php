<?php 
class test_plugin extends Plugin
{
	public function Startup()
	{
		
	}
	public function __construct()
	{
		LeumCore::AddHook('initialize', array($this, 'Startup'));
	}
}

?>