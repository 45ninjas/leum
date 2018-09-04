<?php
/*
Author: Those45Ninjas
Title: shows

This plug-in adds TV show format support to leum
*/

require_once "show.php";
require_once "episode.php";


class Shows extends Plugin
{
	public function __construct()
	{
		LeumCore::AddHook("leum.front.routes", [$this, 'AssignRoutes']);
		LeumCore::AddHook("leum.setup", [$this, 'Setup']);
	}
	public function Setup()
	{
		Message::Create("info", 'setting up Shows plugin');
		Log::Write('setting up Shows plugin', LOG::INFO, "setup.txt");

		// Create the shows table if it does not already exist.
		if(LeumSetup::TableExists('shows_shows'))
		{
			Message::Create("warning", 'shows_shows already exists, skipping');
			Log::Write('shows_shows already exists, skipping', LOG::WARNING, "setup.txt");
		}
		else
		{
			Show::CreateTable(LeumSetup::$dbc);

			Message::Create("success", 'shows_shows was created');
			Log::Write('shows_shows already exists, skipping', LOG::WARNING, "setup.txt");
		}

		// Create the episodes table if it does not already exist.
		if(LeumSetup::TableExists('shows_episodes'))
		{
			Message::Create("warning", 'shows_episodes already exists, skipping');
			Log::Write('shows_episodes already exists, skipping', LOG::WARNING, "setup.txt");
		}
		else
		{
			Episode::CreateTable(LeumSetup::$dbc);

			Message::Create("success", 'shows_episodes was created');
			Log::Write('shows_episodes already exists, skipping', LOG::WARNING, "setup.txt");
		}
	}
	public function AssignRoutes()
	{
		// Add some routes.
	}
}

?>