<?php
class QueryBuilder
{
	function Query()
	{	
		private $wantedTags;
		private $unwantedTags;
		
		public __constructor($queryString)
		{
			foreach (explode(' ', $queryString)) as $part)
			{
				echo "\npart: $part";
			}
		}
		public function GetSql()
		{
			$wantedPlaceholder = LeumCore::PDOPlaceHolder($this->wantedTags);
			$unwantedPlaceholder = LeumCore::PDOPlaceHolder($this->unwantedTags);

			$params = array_merge($this->wantedTags, $this->unwantedTags);

			$sql = "SELECT sql_calc_found_rows media.* from map
			inner join media on map.media_id = media.media_id
			inner join tags on map.tag_id = tags.tag_id
			where tags.slug in ( $wantedPlaceholder )
			and tags.slug not in ( $unwantedPlaceholder ) ";

			$statement = $dbc-> prepare($sql);
			$statement->execute($params);
		}
	}
}
?>
