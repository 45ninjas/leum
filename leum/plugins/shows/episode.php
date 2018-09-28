<?php

class Episode extends Media
{
	public $show;
	public $season;
	public $episode;
	public $media;
	public $episode_id;


	public static function CreateTable($dbc)
	{
		$sql = "CREATE table shows_episodes
		(
			`show` int unsigned,
			slug varchar(32) not null unique key,
			season int not null,
			episode int not null,
			media bigint unsigned not null,
			unique episode_id (`show`, season, episode),
			foreign key (media) references media(id),
			foreign key (`show`) references shows_shows(id)
		);";
		$dbc->exec($sql);
	}
}

?>