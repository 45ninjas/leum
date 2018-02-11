<?php 
class Task
{
	public static $registerdTasks = array();
	public $name;
	public $log;
	public $arguments;
	public $task_id;
	public $total_progress;
	public $progress;
	public $message;

	public function __construct($taskName, $taskArgs, $taskSteps, $logFile = null, $updateRate = 1)
	{
		$this->items = $items;

		if(!isset($logFile))
		{
			$logFile = LOG_DIR . "/task-error.log";
		}
		$this->logFile = $logFile;
	}

	public function Run($function)
	{
		call_user_func_array($function, $this->task);
	}

	private function SetProgress($dbc, $progress, $message = null)
	{
		$this->progress = $progress;

		$vars = array();

		if(isset($message) && is_string($message))
		{
			$sql = "UPDATE task set progress = ?, message = ? where task_id = ?";
			array_push($vars, $progress);
			array_push($vars, $message);
		}
		else
		{
			$sql = "UPDATE task set progress = ? where task_id = ?";
			array_push($vars, $progress);
		}

		array_push($vars, $message);
		$statement = $dbc->execute($vars);
	}

	public static function CreateTable($dbc)
	{
		$sql = "CREATE table task
		(
			task_id int unsigned auto_increment primary key,
			name varchar(32) not null unique key,
			log text not null,
			arguments text not null,
			total_progress int not null,
			progress int default 0,
			message text
		)";

		$dbc->exec($sql);
	}
	public static function Register($name, $function)
	{
		$registerdTasks[] = array("name" => $name, "function" => $function);
	}

	public static function Create($dbc, $name, $arguments, $totalProgress)
	{
		$sql = "INSERT into tasks (name, log, arguments, total_progress) values (?, ?, ?, ?)";

		$statement = $dbc->prepare($sql);
		$statement->execute([$name, $log, $arguments, $totalProgress]);

		return $dbc->lastInsertId();
	}
	public static function GetProgress($dbc, $task_id)
	{
		$sql = "SELECT progress, total_progress, message from tasks where task_id = ?";
		$statement = $dbc->prepare($sql);
		$statement->execute([$task_id]);

		return $statement->fetch();
	}
	public static function GetTasks($dbc, $task)
	{
		if ($task instanceof Task)
		{
			if(isset($task->task_id))
				$task = $task->task_id;
			else
				$task = $task->name;
		}

		if(is_numeric($task))
			$sql = "SELECT * from tasks where task_id = ?";
		else if (is_string($task))
			$sql = "SELECT * from tasks where name = ?";

		$statement = $dbc->prepare($sql);
		$statement->execute([$task]);

		return $statement->fetchAll(PDO::FETCH_CLASS, __CLASS__);
	}
}
?>