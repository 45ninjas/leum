<?php

class Episode
{
	public $show;
	public $season;
	public $episode;
	public $title;
	public $media;
	public $episode_id;


	public static function CreateTable($dbc)
	{
		$sql = "CREATE table shows_episodes
		(
			`show` int not null,
			slug varchar(32) not null unique key,
			season int not null,
			episode int not null,
			title varchar(128) not null,
			media int unsigned not null,
			unique episode_id (`show`, season, episode),
			foreign key (media) references media(media_id),
			foreign key (`show`) references shows_shows(id)
		);";
		$dbc->exec($sql);
	}
}

?>