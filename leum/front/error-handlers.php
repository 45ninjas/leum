<?php

function LeumErrorHandler($errorno, $errstr, $errfile, $errline)
{
	// Write the error.
	$message = "[$errorno] $errstr in $errfile [$errline]";
	Message::Create("error","Error: $message\n", "exception");
	Message::ShowMessages("exception", "content");

	// Log it if we can.
	if(class_exists("Log"))
		Log::Write($message, Log::ERROR);
}

function LeumExceptionHandler($exception, $showMessages = true)
{
	// Write the exception.
	$message = $exception->GetMessage() . " " . $exception->getFile() . "(" . $exception->getLine() .")" . PHP_EOL . $exception->getTraceAsString();
	Message::Create("error","Uncaught Exception: $message\n", "exception");
	
	if($showMessages);
		Message::ShowMessages("exception", "content");

	// Log it if we can.
	if(class_exists("Log"))
		Log::Write($message, Log::EXCEPTION);
}

set_error_handler("LeumErrorHandler");
set_exception_handler("LeumExceptionHandler");

?>
