<?php

class edit_movie implements IPage
{
	public $movie;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		if(!$leum->PermissionCheck("admin-pages", "media-edit"))
			return;

		if(isset($arguments[0]))
		{
			$mediaQuery = new MediaQuery($dbc);
			$mediaQuery->GetSlugs();
			$mediaQuery->Type('movie');
			$this->movie = $mediaQuery->Execute(true);
		}
	}

	public function Content()
	{
		
	}
}

?>
