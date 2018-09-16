<?php

class Show
{
	public $id;
	public $title;
	public $description;
	public $cover_image;
	public $seasons;
	
	public static function CreateTable($dbc)
	{
		$sql = "CREATE table shows_shows
		(
			id int unsigned auto_increment primary key,
			title varchar(128) not null,
			slug varchar(32) not null unique key,
			description text,
			cover_image text
		);";
		$dbc->exec($sql);
	}

	public function GetEpisodes($dbc)
	{
		$sql = "SELECT * from shows_episodes
		where `show` = ?
		order by season, episode desc";
		$statement = $dbc->prepare($sql);
		$statement->execute([$this->id]);

		$this->seasons = [];
		while ($episode = $statement->fetchObject('Episode'))
		{
			if(!isset($this->seasons[$episode->season]))
				$this->seasons[$episode->season] = [];

			$this->seasons[$episode->season][] = $episode;
		}
	}

	public function CoverUrl()
	{
		return ROOT . MEDIA_DIR . "/" . $this->cover_image;
	}

	static function Get($dbc, $slug = null)
	{
		if($slug != null)
		{
			$statement = $dbc->prepare("SELECT * from shows_shows where slug = ?");
			$statement->execute([$slug]);
			return $statement->fetchObject('Show');
		}

		$statement = $dbc->query("SELECT * from shows_shows order by title desc");
		return $statement->fetchAll(PDO::FETCH_CLASS, 'Show');
	}
}