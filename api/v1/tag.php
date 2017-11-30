<?php
include_once "leum.api.php";
class Tag
{
	public $tag_id;
	public $slug;
	public $title;

	public static function Get($dbc,$tag = null)
	{
		// BUG: Minimal does not do what it's supposed to do. It still produces
		// variables in the JSON output just with nulls instead. :/

		// ==== Get All tags ===
		if(!isset($tag))
		{
			$sql = "SELECT * from tags";

			$data = array();
			$statement = $dbc->query($sql);
			
			return $statement->fetchAll(PDO::FETCH_CLASS, 'Tag');
		}
		// ==== Singular tags ====
		if(!is_array($tag))
		{
			if(is_numeric($tag))
				$sql = "SELECT * from tags where tag_id = ?";
			else
				$sql = "SELECT * from tags where slug = ?";
		
			// Execute the query
			$statement = $dbc->prepare($sql);
			$statement->execute([$tag]);

			// Convert the row to a Media class and return it.
			return $statement->fetchObject(__CLASS__);
		}

		// ==== Array of tags ===
		else
		{
			$placeHolder = str_repeat("?, ", count($tag) - 1) . "?";
			if(is_numeric($tag))
				$sql = "SELECT * from tags where tag_id in ( $placeHolder )";
			else
				$sql = "SELECT * from tags where slug = in ( $placeHolder )";
		
			// Execute the query
			$statement = $dbc->prepare($sql);
			$statement->execute($tag);

			// Convert the row to a Media class and return it.
			return $statement->fetchAll(PDO::FETCH_CLASS, 'Tag');
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
			// Inserting a new tag item into the database
			$sql = "INSERT INTO tag (slug, title) VALUES (?, ?)";

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
	public static function FindTagsLike($dbc, $queryString)
	{
		$queryString = strtolower($queryString);
		$queryString = "%$queryString%";
		$sql = "SELECT * FROM tags WHERE title LIKE ?";
		$statement = $dbc->prepare($sql);
		$statement->execute([$queryString]);
		return $statement->fetchAll();
	}
}
?>
