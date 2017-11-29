<?php
include_once "leum.api.php";
class Mapping
{
	public $map_id;
	public $tag_id;
	public $media_id;

	public static function Get($dbc,$index = null)
	{
		// --- Get a single item if the index is defined ---
		if(is_numeric($index))
		{
			$sql = "SELECT * from map where map_id = ?";
		
			$statement = $dbc->prepare($sql);
			$statement->execute([$index]);

			// Convert the row to a Map class and return it.
			return $statement->fetchObject(__CLASS__);
		}

		// --- Get multiple items ---
		else
		{
			// Make some SQL magic
			$sql = "SELECT * from map";

			$data = array();
			$statement = $dbc->query($sql);

			// Convert rows from the database into Map classes.
			while($data[] = $statement->fetchObject(__CLASS__));

			// Remove the last item in the list because it will return false due to
			// the way I'm looping.
			array_pop($data);
			return $data;
		}
	}
	public static function Delete($dbc, $index)
	{
		$sql = "DELETE FROM map WHERE map_id = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$index]);

		$deleted = $statement->rowCount();
		return "Deleted $deleted rows";
	}
	public static function Insert($dbc, $request, $index = null)
	{
		if($request instanceof Map)
			$map = $request;
		else
		{
			$map = new Map();
			$map->media_id = $request['media_id'];
			$map->tag_id = $request['tag_id'];
		}

		if(is_numeric($index))
		{
			// Updating existing map
			$sql = "UPDATE map SET media_id = ?, tag_id = ?, path = ? WHERE map_id = ?";

			$statement = $dbc->prepare($sql);
			$statement->execute([$map->media_id, $map->tag_id, $index]);
		}
		else
		{
			// Inserting a new media item into the database
			$sql = "INSERT INTO map (media_id, tag_id) VALUES (?, ?)";

			$statement = $dbc->prepare($sql);
			$statement->execute([$map->media_id, $map->tag_id]);
		}
	}

	public static function Map($dbc, $media, $tag)
	{
		// Add a tag to a media item.
		
		// First, Get the media ID.
		if ($media instanceof Media)
			$media_id = $media->media_id;
		else
			$media_id = $media;

		// Next get the tag ID.
		if(is_integer($tag))
			$tag_id = $tag;
		elseif ($tag instanceof Tag)
			$tag_id = $tag->tag_id;
		/*elseif (is_string($tag))
		{
			// Looks like the tag is a slug...
			// TODO: Finish Implementation of slug support.
		}*/

		$sql = "INSERT INTO map (media_id, tag_id) VALUES (?, ?)";

		$statement = $dbc->prepare($sql);
		$statement->execute([$media_id, $tag_id]);
	}

	public static function Unmap($dbc, $media, $tag)
	{
		// Remove a tag from a media item.
		
		// First, Get the media ID.
		if ($media instanceof Media)
			$media_id = $media->media_id;
		else
			$media_id = $media;

		// Next get the tag ID.
		if(is_integer($tag))
			$tag_id = $tag;
		elseif ($tag instanceof Tag)
			$tag_id = $tag->tag_id;
		/*elseif (is_string($tag))
		{
			// Looks like the tag is a slug...
			// TODO: Finish Implementation of slug support.
		}*/

		$sql = "DELETE FROM map WHERE media_id =? AND tag_id =?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$media_id, $tag_id]);
	}

	public static function GetMediaTags($dbc, $media)
	{
		// Gets all tags that are mapped to a particular media item.

		// Get the media ID.
		if ($media instanceof Media)
			$media_id = $media->media_id;
		else
			$media_id = $media;

		// Merge three tables together and what not....
		$sql = "SELECT map.map_id, map.tag_id, tags.slug, tags.title FROM map
			INNER JOIN media ON map.media_id = media.media_id
			INNER JOIN tags ON map.tag_id = tags.tag_id
			WHERE media.media_id = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$media_id]);

		// Convert rows from the database into Tag classes.
		while($data[] = $statement->fetchObject("Tag"));

		// Remove the last item in the list because it will return false due to
		// the way I'm looping.
		array_pop($data);

		return $data;
	}
	public static function SetMediaTags($dbc, $media, $newTags = null)
	{
		if ($media instanceof Media)
			$media_id = $media->media_id;
		else
			$media_id = $media;


		// If there is nothing in the user input, just remove all tags.
		if(is_null($newTags) || count($newTags) == 0)
		{
			$sql = "DELETE from map where $media_id = ?";
			$statement = $dbc->prepare($sql);
			$statement->execute([$media_id]);
			return;
		}

		// Looks like the user actually did something.
		
		// Make sure everything in the newtags array is an intger.
		$newTags = array_map('intval', $newTags);

		// See what tags are mapped.
		$sql = "SELECT tag_id from map where media_id = ?";
		$statement = $dbc->prepare($sql);
		$statement->execute([$media_id]);

		$removeTags = array();

		// Go over each tag in the database and figure out what to do with each one.
		
		$tagsInDb = $statement->fetchAll(PDO::FETCH_COLUMN);

		foreach ($tagsInDb as $dbTag)
		{
			if(!in_array($dbTag, $newTags))
				$removeTags[] = $dbTag;
			unset($newTags[array_search($dbTag, $newTags)]);
		}
		// So now we know what to remove and add to the database. Let's start a transaction.
		try
		{
			$dbc->beginTransaction();

			while ($tag = array_pop($removeTags))
				Mapping::Unmap($dbc, $media_id, $tag);

			while ($tag = array_pop($newTags))
				Mapping::Map($dbc, $media_id, $tag);

			$dbc->commit();
		}
		catch (Exception $e)
		{
			$dbc->rollBack();
			throw $e;
		}
	}
}
?>
