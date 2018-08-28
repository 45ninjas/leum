<?php

class Log
{
	const EXCEPTION = 0;
	const ERROR = 1;
	const WARNING = 2;
	const INFO = 3;
	const DEBUG = 4;

	public static $logLevels =
	[
		"exception",
		"error",
		"warning",
		"info",
		"debug"
	];

	public static $highest = false;
	
	public static function Write($message, $type = self::INFO, $log = null)
	{
		if(null !== LOG_LEVEL)
		{
			if($type > self::$highest)
			self::$highest = $type;

			// Make sure the log is actually important enough to save.
			if($type > LOG_LEVEL)
				return;

			if($log == null)
				$logfile = LOG_DIR . "/default.txt";
			else
				$logfile = LOG_DIR . "/$log.txt";

			$typeStr = self::$logLevels[$type];
			
			$date = new DateTime();
			$date = $date->format("y:m:d h:i:s");

			file_put_contents($logfile, "$date [$typeStr] $message" . PHP_EOL, FILE_APPEND);
		}
	}
}

?>