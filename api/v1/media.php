<?php
include_once "leum.api.php";
class Media
{
	public $media_id;
	public $title;
	public $source;
	public $path;
	public $date;

	public function GetLink()
	{
		return ROOT . MEDIA_DIR . $this->path;
	}
	public function GetThumbnail()
	{
		return ROOT . THUMB_DIR . $this->path;
	}
	public function GetPath()
	{
		return SYS_ROOT . MEDIA_DIR . $this->path;
	}
	public function GetTags()
	{
		$dbc = Leum::Instance()->GetDatabase();
		return Mapping::GetMediaTags($dbc, $this->media_id);
	}

	public static function Get($dbc,$index = null)
	{
		// BUG: Minimal does not do what it's supposed to do. It still produces
		// variables in the JSON output just with nulls instead. :/

		// --- Get a single item if the index is defined ---
		if(is_numeric($index))
		{
			// Make some SQL magic.
			$sql = "SELECT * from media where media_id = ?";
		
			// Execute the query
			$statement = $dbc->prepare($sql);
			$statement->execute([$index]);

			// Convert the row to a Media class and return it.
			return $statement->fetchObject(__CLASS__);
		}

		// --- Get multiple items ---
		else
		{
			// Make some SQL magic
			$sql = "SELECT * from media";

			$data = array();
			$statement = $dbc->query($sql);

			// Convert rows from the database into Media classes.
			while($data[] = $statement->fetchObject(__CLASS__));

			// Remove the last item in the list because it will return false due to
			// the way I'm looping.
			array_pop($data);

			// Return the data for the world to see.
			return $data;
		}
	}
	public static function GetPaged($dbc, $page, $pageSize)
	{
		$sql = "SELECT * FROM media LIMIT ";
	}
	public static function Delete($dbc, $index)
	{
		$sql = "DELETE FROM media WHERE media_id = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$index]);

		$deleted = $statement->rowCount();
		return "Deleted $deleted rows";
	}
	public static function Insert($dbc, $request, $index = null)
	{
		if($request instanceof Media)
			$media = $request;
		else
		{
			$media = new Media();
			$media->title = $request['title'];
			$media->source = $request['source'];
			$media->path = $request['path'];
			//$media->index = $index;
		}

		if(is_numeric($index))
		{
			// Updating existing media
			$sql = "UPDATE media SET title = ?, source = ?, path = ? WHERE media_id = ?";

			$statement = $dbc->prepare($sql);
			$statement->execute([$media->title, $media->source, $media->path, $index]);
			return $index;
		}
		else
		{
			// Inserting a new media item into the database
			$sql = "INSERT INTO media (title, source, path) VALUES (?, ?, ?)";

			$statement = $dbc->prepare($sql);
			$statement->execute([$media->title, $media->source, $media->path]);
			return $dbc->lastInsertId();
		}
	}

	// public static function MapTag($dbc, $media, $tag)
	// {
	// 	// Add a tag to a media item.
		
	// 	// First, Get the media ID.
	// 	if(is_integer($media))
	// 		$media_id = $media;
	// 	elseif ($media instanceof Media)
	// 		$media_id = $media->media_id;

	// 	// Next get the tag ID.
	// 	if(is_integer($tag))
	// 		$tag_id = $tag;
	// 	elseif ($tag instanceof Tag)
	// 		$tag_id = $tag->tag_id;
	// 	/*elseif (is_string($tag))
	// 	{
	// 		// Looks like the tag is a slug...
	// 		// TODO: Finish Implementation of slug support.
	// 	}*/

	// 	$sql = "INSERT INTO map (media_id, tag_id) VALUES (?, ?)";

	// 	$statement = $dbc->prepare($sql);
	// 	$statement->execute($media_id, $tag_id);
	// }

	// public static function UnmapTag($dbc, $media, $tag)
	// {
	// 	// Remove a tag from a media item.
		
	// 	// First, Get the media ID.
	// 	if(is_integer($media))
	// 		$media_id = $media;
	// 	elseif ($media instanceof Media)
	// 		$media_id = $media->media_id;

	// 	// Next get the tag ID.
	// 	if(is_integer($tag))
	// 		$tag_id = $tag;
	// 	elseif ($tag instanceof Tag)
	// 		$tag_id = $tag->tag_id;
	// 	elseif (is_string($tag))
	// 	{
	// 		// Looks like the tag is a slug...
	// 		// TODO: Finish Implementation of slug support.
	// 	}

	// 	$sql = "DELETE FROM map WHERE media_id =? AND tag_id =?";

	// 	$statement = $dbc->prepare($sql);
	// 	$statement->execute($media_id, $tag_id);
	// }
	// public static function GetMappedTags($dbc, $media)
	// {
	// 	// Gets all tags that are mapped to a particular media item.
	// 	// For this to work tags must be included.
	// 	require_once("tag.php");

	// 	// Get the media ID.
	// 	if(is_integer($media))
	// 		$media_id = $media;
	// 	elseif ($media instanceof Media)
	// 		$media_id = $media->media_id;

	// 	// Merge three tables together and what not....
	// 	$sql = "SELECT map.map_id, map.tag_id, tags.slug, tags.title FROM map
	// 		INNER JOIN media ON map.media_id = media.media_id
	// 		INNER JOIN tags ON map.tag_id = tags.tag_id
	// 		WHERE media.media_id = ?";

	// 	$statement = $dbc->prepare($sql);
	// 	$statement->execute([$media_id]);

	// 	// Convert rows from the database into Tag classes.
	// 	while($data[] = $statement->fetchObject("Tag"));

	// 	// Remove the last item in the list because it will return false due to
	// 	// the way I'm looping.
	// 	array_pop($data);

	// 	return $data;
	// }
	// public static function SetMappedTags($dbc, $media, $tags)
	// {
		
	// }
}
?>
