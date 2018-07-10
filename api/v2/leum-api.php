<?php
if(!defined('SYS_ROOT'))
	define('SYS_ROOT', realpath(__DIR__ . "/../.."));
define('API_ROOT', __DIR__);

require_once SYS_ROOT . "/leum/core/leum-core.php";

include_once API_ROOT . "/resource/media.php";
include_once API_ROOT . "/resource/tags.php";
include_once API_ROOT . "/resource/content.php";
include_once API_ROOT . "/resource/user.php";

require_once SYS_ROOT . "/leum/functions.php";
require_once SYS_ROOT . "/leum/conf/leum.conf.php";

use API as API;

// Catch all exceptions.
try
{
    $api = new LeumApi($_REQUEST['request']);
    echo $api->ProcessAPI();
}
catch (Exception $e)
{
    echo json_encode(Array('error' => $e->getMessage()));
}

class LeumAPI
{
	public $method;
	public $data;
	public $args;
	public $resource;
	public $dbc;

	public function __construct($request)
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Methods: *");
		header("Content-Type: application/json");

		$this->dbc = DBConnect();

		$this->args = explode('/', rtrim($request, '/'));
		$this->resource = array_shift($this->args);
		$this->resource = preg_replace("/[^[:alnum:]]/u", '', $this->resource);

		$this->method = $_SERVER['REQUEST_METHOD'];
		if($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER))
		{
			if($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE')
				$this->method = 'DELETE';
			else if($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT')
				$this->method = 'PUT';
			else
				throw new Exception("Unexpected Header");
		}

		switch ($this->method)
		{
 			case 'DELETE':
 			case 'POST':
 				$this->data = $this->CleanInputs($_POST);
 				break;

 			case 'GET':
 				$this->data = $this->CleanInputs($_GET);
 				break;

			case 'PUT':
 				$this->data = $this->CleanInputs($_GET);
 				$this->file = file_get_contents("php://input");
 				break;
 			
 			default:
 				$this->Response('Invalid Method', 405);
 				break;
		}
	}
	public function ProcessAPI()
	{
		if(class_exists('\\API\\'.$this->resource))
		{
			$ref = '\\API\\' . $this->resource;
			$object = new $ref($this);
			if(method_exists($object, $this->method))
			{
				$data = call_user_func([$object,$this->method]);
				if($data == null && $data != 0)
					throw new Exception("$this->resource data was empty");
				
				return $this->Response($data);
			}
			else
				throw new Exception("$this->method method does not exist in $this->resource");
		}
		return $this->Response("No Endpoint found $this->resource", 404);
	}
	protected function CleanInputs($data)
	{
		$cleanInput = Array();
		if(is_array($data))
		{
			foreach ($data as $key => $value)
			{
				$cleanInput[$key] = $this->CleanInputs($value);
			}
		}
		else
			$cleanInput = trim(strip_tags($data));

		return $cleanInput;

	}
	protected function Response($data, $status = 200)
	{
		header("HTTP/1.1 $status " . $this->RequestStatus($status));
		return json_encode($data, JSON_PRETTY_PRINT);
	}

	protected function RequestStatus($code)
	{
		$status = array(
			200 => "OK",
			404 => "Not Found",
			405 => "Method Not Allowed",
			500 => "Internal Server Error"
		);
		return ($status[$code])?$status[$code]:$status[500];
	}
}
?>
