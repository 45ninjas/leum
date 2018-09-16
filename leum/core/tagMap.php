<?php
class TagMap
{
	public $tag;
	public $media;
	public $map_id;

	public static function CreateTable($dbc)
	{
		$sql = "CREATE table tag_map
		(
			tag bigint unsigned not null,
			media bigint unsigned not null,
			unique map (tag, media),
			foreign key (media) references media(id) on delete cascade,
			foreign key (tag) references tags(id) on delete cascade
		);

		drop procedure if exists tag_count;

		create procedure tag_count (in tagId bigint)
			select count(tag) into @ammount from tag_map where tag = tagId
			update tags set count = @ammount where id = tagId;
		end;

		drop trigger if exists tag_count_update;
		drop trigger if exists tag_count_delete;
		drop trigger if exists tag_count_updated;

		create trigger tag_count_update after update on tag_map for each row call tag_count(NEW.tag);
		create trigger tag_count_delete after delete on tag_map for each row call tag_count(NEW.tag);
		create trigger tag_count_updated after insert on tag_map for each row call tag_count(NEW.tag);
		";
		$dbc->exec($sql);
	}

	public static function Map($dbc, $media, $tag)
	{
		// get the index for both media and tag.
		$id = self::GetMediaId($media);
		$tag_id = self::GetTagId($tag);

		$sql = "INSERT into tag_map (media, tag) values (?, ?)";

		$statement = $dbc->prepare($sql);
		$statement->execute([$id, $tag_id]);
	}
	public static function Unmap($dbc, $media, $tag)
	{
		// get the index for both media and tag.
		$id = self::GetMediaId($media);
		$tag_id = self::GetTagId($tag);

		$sql = "DELETE from tag_map where media = ? AND tag = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$id, $tag_id]);
	}
	public static function UnmapMultiple($dbc, $media, $tags)
	{
		// Not working correctly, it's been marked as not implemented
		// as it's not essential to the functionality of leum at the moment.
		throw new Exception("UnmapMultiple function not Implemented");
		if($tags == null || count($tags) == 0)
			return 0;

		$id = self::GetMediaId($media);

		$indexPlaceholder = self::PDOPlaceHolder($tags);
		$sql = "DELETE from tag_map where media = ? and tag in ('$indexPlaceholder')";

		$statement = $dbc->prepare($sql);
		
		array_unshift($tags, $id);
		var_dump($tags);
		$statement->execute($tags);
		return $statement->rowCount();
	}
	public static function UnmapAll($dbc, $media)
	{
		$id = self::GetMediaId($media);

		$sql = "DELETE from tag_map where media = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$id]);

		return $statement->rowCount();
	}
	// TODO: Move this to media?
	public static function GetMappedTags($dbc, $media, $slugsOnly = false)
	{
		$id = self::GetMediaId($media);

		if($slugsOnly)
			$sql = "SELECT tags.slug from tag_map";
		else
			$sql = "SELECT tag_map.tag, tags.slug, tags.count from tag_map";

		$sql .= " inner join media ON tag_map.media = media.id";
		$sql .= " inner join tags on tag_map.tag = tags.tag_id";
		$sql .= " where media.id = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$id]);

		if($slugsOnly)
			return $statement->fetchAll(PDO::FETCH_COLUMN);
		else
			return $statement->fetchAll(PDO::FETCH_CLASS, 'Tag');
	}
	public static function SetMappedTags($dbc, $media, $newSlugs = null, $allowTagCreation = false)
	{
		$id = self::GetMediaId($media);

		// Looks like there are no tags so let's delete all maps for this item.
		if(is_null($newSlugs) || count($newSlugs) == 0)
		{
			self::UnmapAll($dbc, $id);
			return "deleted all tags";
		}

		// Get the tag_id's of the new slugs.
		$indexPlaceholder = self::PDOPlaceHolder($newSlugs);
		$sql = "SELECT tags.tag_id FROM tags WHERE tags.slug IN ( $indexPlaceholder )";
		$statement = $dbc->prepare($sql);
		$statement->execute($newSlugs);

		$newTagIds = $statement->fetchAll(PDO::FETCH_COLUMN);

		// Get the tags that are already mapped to this media item.
		$sql = "SELECT tags.tag_id from tag_map inner join tags on tag_map.tag = tags.tag_id where id = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$id]);
		$tagIdsInDb = $statement->fetchAll(PDO::FETCH_COLUMN);

		// Add any new tags.
		if($allowTagCreation == true && count($newTagIds) != count($newSlugs))
		{
			// Looks like we've got tags to add.
			// Figure out what slugs need to be created.
			$indexPlaceholder = self::PDOPlaceHolder($newSlugs);
			$sql = "SELECT tags.slug FROM tags WHERE tags.slug IN ( $indexPlaceholder )";
			$statement = $dbc->prepare($sql);
			$statement->execute($newSlugs);
			$existingSlugs = $statement->fetchAll(PDO::FETCH_COLUMN);

			foreach ($newSlugs as $newSlug)
			{
				if(!in_array($newSlug, $existingSlugs))
				{
					$tagId = Tag::InsertSingle($dbc, $newSlug);
					$newTagIds[] = $tagId;
				}
			}
		}

		// Set-up our remove and add tag arrays.
		$removeTags = array();
		$addTags = $newTagIds;

		// Iterate over each tag that's currently mapped.
		foreach ($tagIdsInDb as $dbTag)
		{
			// if a tag is not in the list of new tags, remove it.
			if(!in_array($dbTag, $newTagIds))
				$removeTags[] = $dbTag;

			// Otherwise remove the current tag from the list of new tags.
			// we don't have to add tags that are already mapped.
			unset($addTags[array_search($dbTag, $newTagIds)]);
		}

		if($dbc->beginTransaction())
		{
			// Apply the changes.
			try
			{
				while ($tag = array_pop($removeTags))
					self::Unmap($dbc, $id, $tag);

				while ($tag = array_pop($addTags))
					self::Map($dbc, $id, $tag);

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
			return $media->id;
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