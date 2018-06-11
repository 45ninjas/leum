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
		if(is_file($this->GetThumbPath()))
			return ROOT . THUMB_DIR . "/" . $this->path . ".jpg";
		else
			return null;
	}
	public function GetPath()
	{
		return SYS_ROOT . MEDIA_DIR . "/" . $this->path;
	}
	public function GetThumbPath()
	{
		return SYS_ROOT . THUMB_DIR . "/" . $this->path . ".jpg";
	}
	public function GetTags($dbc = null, $slugsOnly = false)
	{
		if(isset($this->media_id))
		{
			if(!isset($dbc))
				$dbc = Leum::Instance()->GetDatabase();

			return Mapping::GetMappedTags($dbc, $this->media_id, $slugsOnly);
		}
		else
			return null;
	}
	public function GetDate($format = null)
	{
		$dateTime = new DateTime($this->date);
		if(!isset($format))
			return $dateTime;
		else
			return $dateTime->format($format);
	}
	public function GetMimeType()
	{
		return mime_content_type($this->GetPath());
	}
	public function GetType()
	{
		$type = $this->GetMimeType();
		
		if($type == false)
			return null;
		
		$strings = explode('/', $this->GetMimeType());

		if(isset($strings[0]))
		{
			$type = strtolower($strings[0]);
			$this->type = $type;
			return $type;
		}

		return false;
	}
	public function Delete()
	{
		$dbc = Leum::Instance()->GetDatabase();
		Media::DeleteSingle($dbc, $this);
	}
	public function Update()
	{
		$dbc = Leum::Instance()->GetDatabase();
		Media::UpdateSingle($dbc, $this);
	}

	public static function CreateTable($dbc)
	{
		$sql = "CREATE table media
		(
			media_id int unsigned auto_increment primary key,
			title varchar(256) not null,
			source text not null,
			path text not null,
			date timestamp default current_timestamp
		)";

		$dbc->exec($sql);
	}

	static function GetSingle($dbc, $media)
	{
		$media = self::GetID($media);

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
		$sql .= " order by date desc";

		$statement = $dbc->prepare($sql);
		$statement->execute([$media_ids]);

		// Convert those multiple items into instances.
		$items = array();

		while($items[] = $statement->fetchObject(__CLASS__));

		// Remove the empty object placed on the end from the while loop.
		array_pop($items);

		return $items;
	}
	static function GetAll($dbc, $page = 0, $pageSize = PAGE_SIZE)
	{
		$offset = $pageSize * $page;
		// Get ALL items
		$sql = "SELECT sql_calc_found_rows * from media order by date desc limit ? offset ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$pageSize, $offset]);

		return $statement->fetchAll(PDO::FETCH_CLASS, __CLASS__);
	}
	static function DeleteSingle($dbc, $media)
	{
		$media = self::GetID($media);

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
			if(isset($mediaData->media_id))
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
	public static function GetWithTags($dbc, $tags, $excludeTags, $page, $pageSize = PAGE_SIZE)
	{
		$offset = $page * $pageSize;
		$hasTags = false;

		$parameters = array();

		// Create the sql string.
		$sql = "SELECT sql_calc_found_rows media.* from media
		left join map on media.media_id = map.media_id
		left join tags on map.tag_id = tags.tag_id";

		// Add the required tags to the query.
		if(isset($tags) && count($tags) > 0)
		{
			$tagPlaceholder = LeumCore::PDOPlaceholder($tags);
			$sql .= "\nWHERE tags.slug IN ( $tagPlaceholder )";
			$parameters = array_merge($parameters, $tags);

			$hasTags = true;
		}
		// Add the excluded tags to the query.
		if(isset($excludeTags) && count($excludeTags) > 0)
		{
			$excludePlaceholder = LeumCore::PDOPlaceholder($excludeTags);

			if($hasTags)
				$sql .= "\nAND";
			else
				$sql .= "\nWHERE";

			$sql .= " tags.slug NOT IN ( $excludePlaceholder )";

			$parameters = array_merge($parameters, $excludeTags);
		}

		// Group and limit the query.
		$sql .= "\nGROUP BY media_id order by date desc limit ? offset ?";
		array_push($parameters, $pageSize);
		array_push($parameters, $offset);

		$statement = $dbc->prepare($sql);
		$statement->execute($parameters);

		return $statement->fetchAll(PDO::FETCH_CLASS, __CLASS__);
	}
	private static function GetID($media)
	{
		if($media instanceof Media)
			return $media->media_id;
		else if(is_numeric($media))
			return $media;

		throw new Exception("Bad index input");
	}

	public static function GetFromPath($dbc, $path)
	{
		$sql = "SELECT media_id from media where path = ?";
		$statement = $dbc->prepare($sql);
		$statement->execute([$path]);

		return $statement->fetchAll(PDO::FETCH_COLUMN);
	}
}
?>