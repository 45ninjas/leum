<?php 

class shows implements IPlugin
{
	function __construct()
	{

	}

	function OnInit($core, $manager)
	{
		echo "Init";
	}
}

?>