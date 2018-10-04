<?php

class Movies extends Plugin
{
	public function __construct()
	{
		LeumCore::AddHook("leum.front.routes", [$this, 'AssignRoutes']);
	}
	public function AssignRoutes()
	{
		Dispatcher::AddRoute('movies', 'plugins/movies/pages/browse_movies.php');
		Dispatcher::AddRoute('movies/%slug%', 'plugins/movies/pages/edit_movie.php');
	}
}
?>