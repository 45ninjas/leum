<?php
interface IPage
{
	public function __construct($leum, $dbc, $userInfo, $arguments);
	public function Content();
}
?>
