<?php
class Tag
{
	public $tag_id;
	public $slug;
	public $title;
	public $tags;

	public static function Get($dbc,$index = null)
	{
		// BUG: Minimal does not do what it's supposed to do. It still produces
		// variables in the JSON output just with nulls instead. :/

		// --- Get a single item if the index is defined ---
		if(is_numeric($index))
		{
			$sql = "SELECT * from tags where tag_id = ?";
		
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
			$sql = "SELECT * from tags";

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
	public static function Delete($dbc, $index)
	{
		$sql = "DELETE FROM tags WHERE tag_id = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$index]);

		$deleted = $statement->rowCount();
		return "Deleted $deleted rows";
	}
	public static function Post($dbc, $request, $index = null)
	{
		if(is_numeric($index))
		{
			// Updating existing media
			$sql = "UPDATE tags SET slug = ?, title = ? WHERE tag_id = ?";

			$statement = $dbc->prepare($sql);
			$statement->execute([$request['slug'], $request['title'], $index]);	
		}
		else
		{
			// Inserting a new media item into the database
			$sql = "INSERT INTO media (slug, title) VALUES (?, ?)";

			$statement = $dbc->prepare($sql);
			$statement->execute([$request['slug'], $request['title']]);
		}
	}

	public static function Insert($dbc, $request, $index = null)
	{
		// if the request is not an instance of a Tag class then convert it.
		if($request instanceof Tag)
			$tag = $request;
		else
		{
			$tag = new Tag();
			$tag->title = $request['title'];
			$tag->slug = $request['slug'];
		}

		// See if the index can be used as an index.
		if(is_numeric($index))
		{
			// Update an existing tag.
			$sql = "UPDATE tags SET title = ?, slug = ? WHERE tag_id = ?";
			$statement = $dbc->prepare($sql);
			$statement->execute([$tag->title, $tag->slug, $index]);
		}
		else
		{
			$sql = "INSERT INTO tags (title, slug) VALUES (?,?)";
			$statement = $dbc->prepare($sql);
			$statement->execute([$tag->title, $tag->slug]);
		}
	}
}
?>
