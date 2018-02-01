<?php
include_once "leum.api.php";
class Ingest
{
	public static function Process($dbc, $index)
	{
		$mediaItem = Media::Get($dbc, $index);
		return $mediaItem;
	}
}
?>
