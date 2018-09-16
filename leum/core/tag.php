<?php
class Tag
{
	public $id;
	public $slug;
	public $count;

	public static function CreateTable($dbc)
	{
		$sql = "CREATE table tags
		(
			id bigint unsigned auto_increment primary key,
			slug varchar(32) not null unique key,
			count int not null default '0'
		);

		drop function if exists tag_slug_id;

		create function tag_slug_id (slug varchar(32))
			returns bigint unsigned deterministic
			return (select tags.id from tags where tags.slug = slug)

		drop function if exists tag_id_slug;

		create function tag_id_slug (index bigint unsigned)
			returns varchar(32) deterministic
			return (select tags.slug from tags where tags.id = index)";
		$dbc->exec($sql);
	}

	public static function GetSingle($dbc,$tag)
	{
		// If the tag is a tag item, use it's tag_id field.
		if($tag instanceof Tag)
			$tag = $tag->id;

		if(is_numeric($tag))
			$sql = "SELECT * from tags where id = ?";
		elseif(is_string($tag))
			$sql = "SELECT * from tags where slug = ?";
		else
			throw new exception("Bad tag input");

			// Execute the query
		$statement = $dbc->prepare($sql);
		$statement->execute([$tag]);

		// Convert the row to a Media class and return it.
		return $statement->fetchObject(__CLASS__);
	}
	public static function GetMultiple($dbc, $tags)
	{
		// TODO: Add support for slugs.
		if($tag instanceof Tag)
			die("Getting multiple tags using a tag class is currently not supported.");

		$indexPlaceholder = join(',', array_fill(0, count($tags), '?'));
		if(is_numeric($tags[0]))
			$sql = "SELECT * from tags where id in ( $indexPlaceholder )";
		else
			$sql = "SELECT * from tags where slug = in ( $indexPlaceholder )";
	
		// Execute the query
		$statement = $dbc->prepare($sql);
		$statement->execute($tag);

		return $statement->fetchAll(PDO::FETCH_CLASS, 'Tag');
	}
	public static function GetAll($dbc)
	{
		// TODO: Add support for slugs.
		$sql = "SELECT * from tags";

		$data = array();
		$statement = $dbc->query($sql);
		
		return $statement->fetchAll(PDO::FETCH_CLASS, 'Tag');
	}
	public static function GetAllSlugs($dbc)
	{
				// TODO: Add support for slugs.
		$sql = "SELECT slug from tags";

		$data = array();
		$statement = $dbc->query($sql);
		
		return $statement->fetchAll(PDO::FETCH_COLUMN);
	}
	public static function DeleteSingle($dbc, $tag)
	{
		if($tag instanceof Tag)
			$tag = $tag->id;

		if(is_numeric($tag))
			$sql = "DELETE FROM tags WHERE id = ?";
		elseif(is_string($tag))
			$sql = "DELETE FROM tags WHERE slug = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$tag]);

		return $statement->rowCount();
	}
	public static function DeleteMultiple($dbc, $tagIds)
	{
		// TODO: Add support for slugs.
		if(!is_array($tagIds))
			die("Indexes is not an array");

		$indexPlaceholder = join(',', array_fill(0, count($tagIds), '?'));
		$sql = "DELETE FROM tags WHERE id in ('$indexPlaceholder')";

		$statement = $dbc->prepare($sql);
		$statement->execute($tagIds);

		return $statement->rowCount();
	}
	public static function InsertSingle($dbc, $tagData, $index = null)
	{
		// Get the slug and index based on the data type of the tagData.
		if($tagData instanceof Tag)
		{
			$slug = $tagData->slug;
			if(isset($tagData->id))
				$index = $tagData->id;
		}
		else if(isset($tagData['slug']))
		{
			$slug = $tagData['slug'];
		}
		else
			$slug = $tagData;
		
		// Clean the slug.
		$slug = self::CreateSlug($slug);

		if(isset($index) && is_numeric($index))
		{
			// Updating existing tags
			$sql = "UPDATE tags SET slug = ? WHERE id = ?";

			$statement = $dbc->prepare($sql);
			$statement->execute([$slug, $index]);
			return $index;
		}
		else
		{
			// Inserting a new tag item into the database
			$sql = "INSERT INTO tags (slug) VALUES (?)";

			$statement = $dbc->prepare($sql);
			$statement->execute([$slug]);
			return $dbc->lastInsertId();
		}
	}

	public static function FindLike($dbc, $queryString, $singular = false, $limit = null)
	{
		$arguments = array();
		$queryString = strtolower($queryString);
		$arguments[] = "%$queryString%";
 
		$sql = "SELECT * FROM tags WHERE slug LIKE ? ORDER BY count DESC";

		if(isset($limit) && is_numeric($limit))
		{
			$sql .= " LIMIT ?";
			$arguments[] = $limit;
		}

		$statement = $dbc->prepare($sql);
		$statement->execute($arguments);
		
		if($singular)
			return $statement->fetch();
		else
			return $statement->fetchAll();
	}
}
?>
