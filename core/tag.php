<?php
class Tag
{
	public $tag_id;
	public $slug;
	public $title;

	public static function GetSingle($dbc,$tag)
	{
		// If the tag is a tag item, use it's tag_id field.
		if($tag instanceof Tag)
			$tag = $tag->tag_id;

		if(is_numeric($tag))
			$sql = "SELECT * from tags where tag_id = ?";
		elseif(is_string($tag))
			$sql = "SELECT * from tags where slug = ?";
		else
			die("bad input");

			// Execute the query
		$statement = $dbc->prepare($sql);
		$statement->execute([$tag]);

		// Convert the row to a Media class and return it.
		return $statement->fetchObject(__CLASS__);
	}
	public static function GetMultiple($dbc, $tags)
	{
		if($tag instanceof Tag)
			die("Getting multiple tags using a tag class is currently not supported.");

		$indexPlaceholder = join(',', array_fill(0, count($tags), '?'));
		if(is_numeric($tags[0]))
			$sql = "SELECT * from tags where tag_id in ( $indexPlaceholder )";
		else
			$sql = "SELECT * from tags where slug = in ( $indexPlaceholder )";
	
		// Execute the query
		$statement = $dbc->prepare($sql);
		$statement->execute($tag);

		return $statement->fetchAll(PDO::FETCH_CLASS, 'Tag');
	}
	public static function GetAll($dbc)
	{
		$sql = "SELECT * from tags";

		$data = array();
		$statement = $dbc->query($sql);
		
		return $statement->fetchAll(PDO::FETCH_CLASS, 'Tag');
	}
	public static function DeleteSingle($dbc, $tagId)
	{
		$sql = "DELETE FROM tags WHERE tag_id = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$tagId]);

		return $statement->rowCount();
	}
	public static function DeleteMultiple($dbc, $tagIds)
	{
		if(!is_array($tagIds))
			die("Indexes is not an array");

		$indexPlaceholder = join(',', array_fill(0, count($tagIds), '?'));
		$sql = "DELETE FROM tags WHERE tag_id in ('$indexPlaceholder')";

		$statement = $dbc->prepare($sql);
		$statement->execute($tagIds);

		return $statement->rowCount();
	}
	public static function InsertSingle($dbc, $tagData, $index = null)
	{
		if($tagData instanceof Tag)
		{
			$tag = $tagData;
			$index = $tagData->tag_id;
		}
		else
		{
			$tag = new Tag();
			$tag->slug = $tagData['slug'];
			$tag->title = $tagData['title'];
		}

		if(is_numeric($index))
		{
			// Updating existing tags
			$sql = "UPDATE tags SET slug = ?, title = ? WHERE tag_id = ?";

			$statement = $dbc->prepare($sql);
			$statement->execute([$tag->slug, $tag->slug, $index]);
			return $index;
		}
		else
		{
			// Inserting a new tag item into the database
			$sql = "INSERT INTO tags (slug, title) VALUES (?, ?)";

			$statement = $dbc->prepare($sql);
			$statement->execute([$tag->slug, $tag->slug]);
			return $dbc->lastInsertId();
		}
	}

	public static function FindLike($dbc, $queryString)
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
