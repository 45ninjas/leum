<?php

class Show
{
	public $show;
	public $title;
	public $description;
	public $cover_image;
	

	public static function CreateTable($dbc)
	{
		$sql = "CREATE table shows_shows
		(
			id int auto_increment primary key,
			title varchar(128) not null,
			slug varchar(32) not null unique key,
			description text,
			cover_image text
		);";
		$dbc->exec($sql);
	}
}