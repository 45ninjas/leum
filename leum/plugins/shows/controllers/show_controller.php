<?php

/**
 * Controller for a show
 */
class ShowController
{
	function ObtainData($input = INPUT_POST)
	{
		$show = new Show();

		$show->title = filter_input($input, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

		$show->id = filter_input($input, 'id', FILTER_VALIDATE_INT);
		$show->slug = filter_input($input, 'slug', FILTER_CALLBACK, array('options' => 'LeumCore::CreateSlug'));

		$show->description = filter_input($input, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
		$show->cover_image = filter_input($input, 'cover_image', FILTER_UNSAFE_RAW);

		return $show;
	}

	function UpdateShow($show)
	{

	}
	function CreateShow($show)
	{
		$params = arary();
		// Only set the values if they actually exist.
		if(isset($show->title))
			array_push($params, ['title'=>$show->title]);

		if(isset($show->description))
			array_push($params, ['description'=>$show->description]);

		if(isset($show->slug))
			array_push($params, ['slug'=>$show->slug]);
		
		if(isset($show->cover_image))
			array_push($params, ['cover_image'=>$show->cover_image]);

		$sqlInsert = implode(', ', array_keys($params));
		$sqlParams = LeumCore::PDOPlaceholder($params);

		$sql = "INSERT INTO shows_shows ( $sqlInsert )
		VALUES ( $sqlParams )";

		$statement = LeumCore::$dbc->prepare($sql);
		array_push($params, $show->id);

		$statement->Execute($params);
	}
}

?>