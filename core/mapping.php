<?php
class Mapping
{
	public $map_id;
	public $tag_id;
	public $media_id;

	public static function CreateTable($dbc)
	{
		$sql = "CREATE table map
		(
			map_id int unsigned auto_increment primary key,
			tag_id int unsigned not null,
			media_id int unsigned not null,
			foreign key (media_id) references media(media_id) on delete cascade,
			foreign key (tag_id) references tags(tag_id) on delete cascade
		)";
		$dbc->exec($sql);
	}

	public static function GetSingle($dbc, $index)
	{
		$sql = "SELECT * from map where map_id = ?";
	
		$statement = $dbc->prepare($sql);
		$statement->execute([$index]);

		// Convert the row to a Map class and return it.
		return $statement->fetchObject(__CLASS__);
	}
	public static function GetMultiple($dbc, $indexes)
	{
		if(!is_array($indexes))
			die("Indexes is not an array.");
		// Get multiple items.
		$indexPlaceholder = Mapping::PDOPlaceHolder($indexes);
		$sql = "SELECT * from map where map_id in ('$indexPlaceholder')";

		$statement = $dbc->prepare($sql);
		$statement->execute([$indexes]);

		// Convert those multiple items into instances.
		$maps = array();

		while($maps[] = $statement->fetchObject(__CLASS__));

		// Remove the empty object placed on the end from the while loop.
		array_pop($maps);

		return $maps;
	}
	public static function GetAll($dbc, $indexes)
	{
		// Make some SQL magic
		$sql = "SELECT * from map";

		$maps = array();
		$statement = $dbc->query($sql);

		// Convert rows from the database into Map classes.
		while($maps[] = $statement->fetchObject(__CLASS__));

		// Remove the last item in the list because it will return false due to
		// the way I'm looping.
		array_pop($maps);
		return $maps;
	}
	public static function DeleteSingle($dbc, $map)
	{
		if($indexes instanceof Map)
			$map = $map->map_id;

		if(!is_numeric($media))
			die("bad input");

		$sql = "DELETE from map where map_id = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$map]);

		return $statement->rowCount();
	}
	public static function DeleteMultiple($dbc, $indexes)
	{
		if(!is_array($indexes))
			die("Indexes is not an array");

		$indexPlaceholder = Mapping::PDOPlaceHolder($indexes);
		$sql = "DELETE from map where map_id in ('$indexPlaceholder')";

		$statement = $dbc->prepare($sql);
		$statement->execute($indexes);

		return $statement->rowCount();
	}
	public static function InsertSingle($dbc, $mapData, $index = null)
	{
		if($mapData instanceof Map)
		{
			$map = $mapData;
			$index = $map->map_id;
		}
		else
		{
			$map = new Map();
			$map->media_id = $mapData["media_id"];
			$map->tag_id = $mapData["tag_id"];
		}

		if(is_numeric($index))
		{
			$sql = "UPDATE map set media_id = ?, tag_id = ? where map_id = ?";
			$statement->execute([$map->media_id, $map->tag_id]);
			return $index;
		}
		else
		{
			$sql = "INSERT into map (media_id, tag_id) values (?, ?)";

			$statement = $dbc->prepare($sql);
			$statement->execute([$map->media_id, $map->tag_id]);
			return $dbc->lastInsertId();
		}
	}
	public static function Map($dbc, $media, $tag)
	{
		// get the index for both media and tag.
		$media_id = Mapping::GetMediaId($media);
		$tag_id = Mapping::GetTagId($tag);

		$sql = "INSERT into map (media_id, tag_id) values (?, ?)";

		$statement = $dbc->prepare($sql);
		$statement->execute([$media_id, $tag_id]);
	}
	public static function MapMultiple($dbc, $media, $tags)
	{
		// TODO: Implement this.
		//throw new Exception("Not implemented");

		/*$media_id = Mapping::GetMediaId($media);

		$indexPlaceholder = Mapping::PDOPlaceHolder($tags);
		$sql = "INSERT into map where media_id in ('$indexPlaceholder')";

		$statement = $dbc->prepare($sql);
		$statement->execute(array_unshift($tags, $media_id));

		return $statement->rowCount();*/
	}
	public static function Unmap($dbc, $media, $tag)
	{
		// get the index for both media and tag.
		$media_id = Mapping::GetMediaId($media);
		$tag_id = Mapping::GetTagId($tag);

		$sql = "DELETE from map where media_id = ? AND tag_id = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$media_id, $tag_id]);
	}
	public static function UnmapMultiple($dbc, $media, $tags)
	{
		// Not working correctly, it's been marked as not implemented
		// as it's not essential to the functionality of leum at the moment.
		throw new Exception("UnmapMultiple function not Implemented");
		if($tags == null || count($tags) == 0)
			return 0;

		$media_id = Mapping::GetMediaId($media);

		$indexPlaceholder = Mapping::PDOPlaceHolder($tags);
		$sql = "DELETE from map where media_id = ? and tag_id in ('$indexPlaceholder')";

		$statement = $dbc->prepare($sql);
		
		array_unshift($tags, $media_id);
		var_dump($tags);
		$statement->execute($tags);
		return $statement->rowCount();
	}
	public static function UnmapAll($dbc, $media)
	{
		$media_id = Mapping::GetMediaId($media);

		$sql = "DELETE from map where media_id = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$media_id]);

		return $statement->rowCount();
	}
	// TODO: Move this to media?
	public static function GetMappedTags($dbc, $media)
	{
		$media_id = Mapping::GetMediaId($media);

		$sql = "SELECT map.map_id, map.tag_id, tags.slug, tags.title from map
		inner join media ON map.media_id = media.media_id
		inner join tags on map.tag_id = tags.tag_id
		where media.media_id = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$media_id]);

		return $statement->fetchAll(PDO::FETCH_CLASS, 'Tag');
	}
	public static function SetMappedTags($dbc, $media, $newSlugs = null)
	{
		$media_id = Mapping::GetMediaId($media);

		// Looks like there are no tags so let's delete all maps for this item.
		if(is_null($newSlugs) || count($newSlugs) == 0)
		{
			Mapping::UnmapAll($dbc, $media_id);
			return;
		}

		// Get the tag_id's of the new slugs.
		$indexPlaceholder = Mapping::PDOPlaceHolder($newSlugs);
		$sql = "SELECT tags.tag_id FROM tags WHERE tags.slug IN ( $indexPlaceholder )";
		$statement = $dbc->prepare($sql);
		$statement->execute($newSlugs);

		$newTags = $statement->fetchAll(PDO::FETCH_COLUMN);

		// Get the tags that are already mapped to this media item.
		$sql = "SELECT tags.tag_id from map inner join tags on map.tag_id = tags.tag_id where media_id = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$media_id]);
		$tagsInDb = $statement->fetchAll(PDO::FETCH_COLUMN);

		// Set-up our remove and add tag arrays.
		$removeTags = array();
		$addTags = $newTags;

		// Iterate over each tag that's currently mapped.
		foreach ($tagsInDb as $dbTag)
		{
			// if a tag is not in the list of new tags, remove it.
			if(!in_array($dbTag, $newTags))
				$removeTags[] = $dbTag;

			// Otherwise remove the current tag from the list of new tags.
			// we don't have to add tags that are already mapped.
			unset($addTags[array_search($dbTag, $newTags)]);
		}

		if($dbc->beginTransaction())
		{
			// Apply the changes.
			try
			{
				while ($tag = array_pop($removeTags))
					Mapping::Unmap($dbc, $media_id, $tag);

				while ($tag = array_pop($addTags))
					Mapping::Map($dbc, $media_id, $tag);

				$dbc->commit();
			}
			catch (Exception $e)
			{
				if($dbc->inTransaction())
					$dbc->rollBack();

				throw $e;
			}
		}
		else
			throw new Exception("Unable to begin a transaction on database.");
	}

	private static function GetMediaId($media)
	{
		// Get the index for the media item.
		if($media instanceof Media)
			return $media->media_id;
		elseif(is_numeric($media))
			return $media;
		else
			die("bad input");
	}
	private static function GetTagId($tag)
	{
		if($tag instanceof Tag)
			return $tag->tag_id;
		elseif(is_numeric($tag))
			return $tag;
		else
			die("bad input");
	}
	private static function PDOPlaceHolder($array)
	{
		return str_repeat('?, ', count($array) - 1) . '?';
	}
}
?>