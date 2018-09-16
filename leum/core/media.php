<?php
class Media
{
	public $id;
	public $type;
	public $parent;
	public $title;
	public $description;
	public $file;
	public $date;

	public static function CreateTable($dbc)
	{
		// Create the media table.
		$sql = "CREATE table media
		(
			id bigint unsigned auto_increment primary key,
			parent bigint unsigned references id,
			type varchar(32),
			title varchar(256),
			description text,
			file text,
			date timestamp default current_timestamp
		);";

		$dbc->exec($sql);
	}

	public function GetLink()
	{
		return ROOT . MEDIA_DIR . "/" . $this->file;
	}
	public function GetThumbnail()
	{
		if(is_file($this->GetThumbPath()))
			return ROOT . THUMB_DIR . "/" . $this->file . ".jpg";
		else
			return null;
	}
	public function GetPath()
	{
		return SYS_ROOT . MEDIA_DIR . "/" . $this->file;
	}
	public function GetThumbPath()
	{
		return SYS_ROOT . THUMB_DIR . "/" . $this->file . ".jpg";
	}
	public function GetTags($dbc, $slugsOnly = false)
	{
		if(isset($this->id))
		{
			return TagMap::GetMappedTags($dbc, $this->id, $slugsOnly);
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

	static function GetSingle($dbc, $media)
	{
		$media = self::GetID($media);

		// Make some SQL magic.
		$sql = "SELECT * from media where id = ?";
		
		// Execute the query
		$statement = $dbc->prepare($sql);
		$statement->execute([$media]);

		// Convert the row to a Media class and return it.
		return $statement->fetchObject(__CLASS__);
	}
	static function GetMultiple($dbc, $ids)
	{
		if(!is_array($ids))
			die("Indexes is not an array.");
		// Get multiple items.
		$indexPlaceholder = join(',', array_fill(0, count($ids), '?'));
		$sql = "SELECT * from media where id in ('$indexPlaceholder')";
		$sql .= " order by date desc";

		$statement = $dbc->prepare($sql);
		$statement->execute([$ids]);

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

		$sql = "DELETE from media where id = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$media]);

		return $statement->rowCount();
	}

	static function DeleteMultiple($dbc, $indexes)
	{
		if(!is_array($indexes))
			die("Indexes is not an array.");

		$indexPlaceholder = join(',', array_fill(0, count($indexes), '?'));
		$sql = "DELETE from media where id in ('$indexPlaceholder')";

		$statement = $dbc->prepare($sql);
		$statement->execute($indexes);

		return $statement->rowCount();
	}
	public static function InsertSingle($dbc, $mediaData, $index = null)
	{
		if($mediaData instanceof Media)
		{
			$media = $mediaData;
			if(isset($mediaData->id))
				$index = $mediaData->id;
		}
		else
		{
			$media = new Media();
			$media->title = $mediaData['title'];
			$media->description = $mediaData['description'];
			$media->file = $mediaData['path'];
		}
		if(is_numeric($index))
		{
			// Updating existing media
			$sql = "UPDATE media SET title = ?, description = ?, file = ? WHERE id = ?";

			$statement = $dbc->prepare($sql);
			$statement->execute([$media->title, $media->description, $media->file, $index]);
			return $index;
		}
		else
		{
			// Inserting a new media item into the database
			$sql = "INSERT INTO media (title, description, file) VALUES (?, ?, ?)";

			$statement = $dbc->prepare($sql);
			$statement->execute([$media->title, $media->description, $media->file]);
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
		left join tag_map on media.id = tag_map.media
		left join tags on tag_map.tag = tags.id";

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
		$sql .= "\nGROUP BY id order by date desc limit ? offset ?";
		array_push($parameters, $pageSize);
		array_push($parameters, $offset);

		$statement = $dbc->prepare($sql);
		$statement->execute($parameters);

		return $statement->fetchAll(PDO::FETCH_CLASS, __CLASS__);
	}
	private static function GetID($media)
	{
		if($media instanceof Media)
			return $media->id;
		else if(is_numeric($media))
			return $media;

		throw new Exception("Bad index input");
	}

	public static function GetFromPath($dbc, $path)
	{
		$sql = "SELECT id from media where file = ?";
		$statement = $dbc->prepare($sql);
		$statement->execute([$file]);

		return $statement->fetchAll(PDO::FETCH_COLUMN);
	}
}
?>