<?php
class Media
{
	public $media_id;
	public $title;
	public $source;
	public $path;
	public $date;
	public $type;

	public function GetLink()
	{
		return ROOT . MEDIA_DIR . "/" . $this->path;
	}
	public function GetThumbnail()
	{
		$thumb = THUMB_DIR . "/" . $this->path . ".jpg";
		
		if(file_exists(SYS_ROOT . $thumb))
			return ROOT. $thumb;
		else
			return null;
	}
	public function GetPath()
	{
		return SYS_ROOT . MEDIA_DIR . "/" . $this->path;
	}
	public function GetTags()
	{
		$dbc = Leum::Instance()->GetDatabase();
		return Mapping::GetMediaTags($dbc, $this->media_id);
	}
	public function GetMimeType()
	{
			return mime_content_type($this->GetPath());
	}
	public function Delete()
	{
		$dbc = Leum::Instance()->GetDatabase();
		Media::DeleteSingle($dbc, $this);
	}
	public function Update()
	{
		$dbc = Leum::Instance()->GetInsatnce();
		Media::UpdateSingle($dbc, $this);
	}

	static function GetSingle($dbc, $media)
	{
		if($media instanceof Media)
			$media = $media->media_id;

		if(!is_numeric($media))
			die("bad input");

		// Make some SQL magic.
		$sql = "SELECT * from media where media_id = ?";
		
		// Execute the query
		$statement = $dbc->prepare($sql);
		$statement->execute([$media]);

		// Convert the row to a Media class and return it.
		return $statement->fetchObject(__CLASS__);
	}
	static function GetMultiple($dbc, $media_ids)
	{
		if(!is_array($media_ids))
			die("Indexes is not an array.");
		// Get multiple items.
		$indexPlaceholder = join(',', array_fill(0, count($media_ids), '?'));
		$sql = "SELECT * from media where media_id in ('$indexPlaceholder')";

		$statement = $dbc->prepare($sql);
		$statement->execute([$media_ids]);

		// Convert those multiple items into instances.
		$items = array();

		while($items[] = $statement->fetchObject(__CLASS__));

		// Remove the empty object placed on the end from the while loop.
		array_pop($items);

		return $items;
	}
	static function GetAll($dbc)
	{
		// Get ALL items
		$sql = "SELECT * from media";

		$statement = $dbc->query($sql);

		// Convert those multiple items into instances.
		$items = array();

		while($items[] = $statement->fetchObject(__CLASS__));

		// Remove the empy object placed on the end from the while loop.
		array_pop($items);

		return $items;
	}

	static function DeleteSingle($dbc, $media)
	{
		if($media instanceof Media)
			$media = $media->media_id;

		if(!is_numeric($media))
			die("bad input");

		$sql = "DELETE from media where media_id = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$media]);

		return $statement->rowCount();
	}

	static function DeleteMultiple($dbc, $indexes)
	{
		if(!is_array($indexes))
			die("Indexes is not an array.");

		$indexPlaceholder = join(',', array_fill(0, count($indexes), '?'));
		$sql = "DELETE from media where media_id in ('$indexPlaceholder')";

		$statement = $dbc->prepare($sql);
		$statement->execute($indexes);

		return $statement->rowCount();
	}
	public static function InsertSingle($dbc, $mediaData, $index = null)
	{
		if($mediaData instanceof Media)
		{
			$media = $mediaData;
			$index = $mediaData->media_id;
		}
		else
		{
			$media = new Media();
			$media->title = $mediaData['title'];
			$media->source = $mediaData['source'];
			$media->path = $mediaData['path'];
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
}
?>