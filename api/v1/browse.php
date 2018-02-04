<?php
include_once "leum.api.php";
class Browse
{
	public static function GetItems($dbc, $tags, $page = null, $pageSize = null)
	{
		// Get all the items....
		$sql = "SELECT sql_calc_found_rows * FROM media";
		$arguments = array();

		// Looks like there are some tags...
		if(is_array($tags) && count($tags) > 0)
		{
			for ($i=0; $i < count($tags); $i++)
			{ 
				if($tags[$i] instanceof Tag)
					$tags[$i] = $tags[$i]->slug;
			}
			// Change the sql to only get items with the tags.
			$tagsPlaceholder = str_repeat('?, ', count($tags) - 1) . "?";
			$sql = "SELECT sql_calc_found_rows media.* FROM map
				INNER JOIN media ON map.media_id = media.media_id
				INNER JOIN tags ON map.tag_id = tags.tag_id
				WHERE tags.slug IN ( $tagsPlaceholder )
				GROUP BY media_id";

			$arguments = $tags;
		}

 		if(isset($page))
 		{
			if(!isset($pageSize))
				$pageSize = 50;

			$sql .= " LIMIT ? OFFSET ?";
			$arguments[] = $pageSize;
			$arguments[] = $page * $pageSize;
		}

		$statement = $dbc->prepare($sql);
		$statement->execute($arguments);

		return $statement->fetchAll(PDO::FETCH_CLASS, "Media");
	}
	public function GetTagsFromSlugs($dbc, $tagSlugs)
	{
		$slugsPlaceholder = str_repeat('?, ', count($tagSlugs) - 1 ) . "?";
		$sql = "SELECT * FROM tags WHERE slug IN ( $slugsPlaceholder )";
		$statement = $dbc->prepare($sql);
		$statement->execute($tagSlugs);

		return $statement->fetchAll(PDO::FETCH_CLASS, "Tag");
	}
	public static function GetTotalItems($dbc)
	{
		$sql = "SELECT found_rows()";

		$statement = $dbc->prepare($sql);
		$statement->execute();

		return $statement->fetch()["found_rows()"];

		/*$statement = $connection->prepare("SELECT FOUND_ROWS();");
		$statement->execute();
		return $statement->fetch()["FOUND_ROWS()"];*/
	}
	public static function GetModalItem($dbc, $mediaId)
	{
		$media = Media::GetSingle($dbc, $mediaId);

		include "../../page-parts/item-preview.php";
		$itemPreview = new ItemPreview($media, true);
		ob_start();
		$itemPreview->Show();
		$media->html = ob_get_clean();

		return $media;
	}
}
?>