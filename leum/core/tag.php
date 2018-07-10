<?php
class Tag
{
	public $tag_id;
	public $slug;
	public $count;

	public static function CreateTable($dbc)
	{
		$sql = "CREATE table tags
		(
			tag_id int unsigned auto_increment primary key,
			slug varchar(32) not null unique key,
			count int not null default '0'
		)";
		$dbc->exec($sql);
	}

	public static function CreateSlug($string)
	{
		// https://web.archive.org/web/20130208144021/http://neo22s.com/slug
		// everything to lower and no spaces begin or end
		$string = strtolower(trim($string));

		$a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ'); 
		$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o'); 
		$string = str_replace($a, $b,$string);

		// adding - for spaces and union characters
		$find = array(' ', '&', '\r\n', '\n', '+',',');
		$string = str_replace ($find, '-', $string);

		//delete and replace rest of special chars
		$find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
		$repl = array('', '-', '');
		$string = preg_replace ($find, $repl, $string);

		//return the friendly url
		return substr($string, 0, 32);
	}

	public static function GetSingle($dbc,$tag)
	{
		// TODO: Add support for slugs.

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
		// TODO: Add support for slugs.
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
			$tag = $tag->tag_id;

		if(is_numeric($tag))
			$sql = "DELETE FROM tags WHERE tag_id = ?";
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
		$sql = "DELETE FROM tags WHERE tag_id in ('$indexPlaceholder')";

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
			if(isset($tagData->tag_id))
				$index = $tagData->tag_id;
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
			$sql = "UPDATE tags SET slug = ? WHERE tag_id = ?";

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
