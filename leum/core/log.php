<?php

class Log
{
	// Log Levels. These are here to make it easier to use the write function.
	// Log::ERROR is easier to remember what number it is.
	const EXCEPTION = 0;
	const ERROR = 1;
	const WARNING = 2;
	const INFO = 3;
	const DEBUG = 4;

	// Used to convert log levels to random letters.
	public static $logLevels =
	[
		"exception",
		"error",
		"warning",
		"info",
		"debug"
	];

	// This is kept to track the highest written error. Mainly used to tell the user
	// when something was written to the logs that they might need to see.
	public static $highest = false;
	
	/**
	 * Write writes data to the logs. Formatting and timestamps done for you.
	 * @param string $message the message to write in the logs
	 * @param int $level 	the level of message using the constants provided in this class
	 * @param string $logs 	the log to save it in. Null is 'default.txt'.
	 */
	public static function Write($message, $level = self::INFO, $log = null)
	{
		// Make sure the log is actually important enough to save.
		if($level > LOG_LEVEL)
			return;

		if($level > self::$highest)
		self::$highest = $level;

		if($log == null)
			$logfile = LOG_DIR . "/default.txt";
		else
			$logfile = LOG_DIR . "/$log";

		$levelStr = self::$logLevels[$level];
		
		$date = new DateTime();
		$date = $date->format("y:m:d h:i:s");

		file_put_contents($logfile, "$date [$levelStr] $message" . PHP_EOL, FILE_APPEND);
	}
}

?>