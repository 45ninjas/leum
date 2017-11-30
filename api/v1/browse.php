<?php
include_once "leum.api.php";
class Browse
{
	public static function GetItems($dbc, $tagSlugs, $page = null, $pageSize = null)
	{
		// Get all the items....
		$sql = "SELECT sql_calc_found_rows * FROM media";
		$arguments = array();

		// Looks like there are some tags... change the sql to only items
		// with the supplied tags.
		if(is_array($tagSlugs) && count($tagSlugs) > 0)
		{
			$tagsPlaceholder = str_repeat('?, ', count($tagSlugs) - 1) . "?";
			$sql = "SELECT sql_calc_found_rows media.* FROM map
				INNER JOIN media ON map.media_id = media.media_id
				INNER JOIN tags ON map.tag_id = tags.tag_id
				WHERE tags.slug IN ( $tagsPlaceholder )
				GROUP BY media_id";

			$arguments = $tagSlugs;
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
}
?>
