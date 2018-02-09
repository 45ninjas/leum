<?php
class QueryBuilder
{
	function Query()
	{
		// Query operators
		// '-' Does not have
		// '~' Can have, not essential
		// coldplay live
		// Search media with both coldplay and live tags.
		// ~coldplay ~live
		// Search media with coldplay or live tags.
		// coldplay -live
		// search media with the coldplay but exclude any -live tags
		
		function ParseQuery($query)
		{
			$queryParts = explode(' ', $query)
		}
	}
}
?>
