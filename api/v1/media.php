<?php
class Media
{
	public $media_id;
	public $title;
	public $source;
	public $path;
	public $date;

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
		if(is_numeric($index))
		{
			// Updating existing media
			$sql = "UPDATE media SET title = ?, source = ?, path = ? WHERE media_id = ?";

			$statement = $dbc->prepare($sql);
			
			if($request instanceof Media)
				$statement->execute([$request->title, $request->source, $request->path, $index]);
			else
				$statement->execute([$request['title'], $request['source'], $request['path'], $index]);	
		}
		else
		{
			// Inserting a new media item into the database
			$sql = "INSERT INTO media (title, source, path) VALUES (?, ?, ?)";

			$statement = $dbc->prepare($sql);

			if($request instanceof Media)
				$statement->execute([$request->title, $request->source, $request->path]);
			else
				$statement->execute([$request['title'], $request['source'], $request['path']]);
		}
	}
}
?>
