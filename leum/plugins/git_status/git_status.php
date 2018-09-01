<?php
/*
Author: Those45Ninjas
Title: Git Status

This plug-in shows how to make a very basic plug-in.
It shows the latest git commit in the footer of every page.
*/

class Git_Status extends Plugin
{
	// Store the hash, date and message from the git command.
	private $hash;
	private $date;
	private $message;
	private $successful = false;

	public function __construct()
	{
		// Add the function Initialize to the initialize hook.
		LeumCore::AddHook("initialize", [$this, 'Initialize']);

		// Add the ShowStatus hook to the leum.front.footer hook.
		LeumCore::AddHook("leum.front.footer", [$this, 'ShowStatus']);
	}
	public function Initialize()
	{
		// Run the git command to return a comma separated list of hash, date and message
		// of the most recent version.
		exec('git log --pretty=%s,%cd,%h --date=relative -n1 HEAD', $output, $return);

		// If git returned an error, bail.
		if($return)
			return;

		// Convert the git output into an array called gitlog.
		$gitlog = explode(',', $output[0]);

		// If we did not get three items, bail.
		if(count($gitlog) != 3)
			return;

		// Shove each variable from the gitlog array into the proper variables.
		$this->hash = array_pop($gitlog);
		$this->date = array_pop($gitlog);
		$this->message = array_pop($gitlog);

		// Set successful to make ShowStatus actually show something.
		$this->successful = true;
	}
	public function ShowStatus()
	{
		// If the initialize process was not successful bail.
		if(!$this->successful)
			return;

		$link = "https://github.com/Those45Ninjas/leum/commit/$this->hash";

		// Show the output in some pretty html for the browser to parse.
		echo '<div class="content foot-note">' . PHP_EOL;
		echo "\t<p>Last commit: <a href=\"$link\">[$this->hash]</a> $this->message $this->date</p>" . PHP_EOL;
		echo '</div>' . PHP_EOL;
	}
}

?>