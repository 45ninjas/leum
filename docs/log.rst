Log
===

Leum logs exceptions and PHP errors to the ``logs/default.txt`` file by default.

Custom messages can be logged to the default.txt or any other log file name using a simple ``Log\:\:Write("Hello World");`` call.


Log::Write
----------

.. code-block:: php

	public static void Log::Write(string $message [ int $level = LOG::INFO, string $logFile ] )

Logs the message to a log file. Timestamp and level is added automatically.

Parameters
""""""""""
$message
	The message to write to the log.
$level (optional)
	The log level of this message. By default it's ``LOG::INFO``. See `Log Levels`_.
$logFile (optional)
	The name of the file to log the message to. If set to **null** the log file default.txt is used.

Example
"""""""
.. code-block:: php

	<?php
		Log::Write("This warning is important!", LOG::WARNING);
		Log::Write("The secret page was accessed", LOG::INFO, 'secret-access.txt');

		// Writes the following to the log/default.txt
		// 18:09:02 04:03:23 [warning] This warning is important!
		// And writes the following to the log/secret-access.txt file.
		// 18:09:02 04:07:36 [info] The secret page was accessed

	?>

Log Levels
----------

LOG\:\:EXCEPTION (int 0)
	This is reserved for uncaught exceptions. This is the lowest level at zero.
LOG\:\:ERROR (int 1)
	Used for logging errors including PHP errors.
LOG\:\:WARNING (int 2)
	Warnings, leum can still function however features or functionality might be lacking.
LOG\:\:INFO (int 3)
	Information that is not all to important.
LOG\:\:DEBUG (int 4)
	Debugging information, Currently not used as using echo is much easier for rapid prototyping.

