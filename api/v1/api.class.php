<?php
abstract class API
{
	/**
	 * Property: method
	 * The HTTP method this request was made in, GET, POST, PUT, DELETE ect.
	*/
	protected $method = '';
	/**
	 * Property: endpoint
	 * The Model requested in the uri. Eg: /files
	*/
	protected $endpoint = '';
	/**
	 * Property: verb
	 * An optional additional descriptor about the endpoint, used for things that can't
	 * be handled by basic methods. Eg: /files/process
	*/
	//protected $verb = '';
	/**
	 * Property: args
	 * Any additional URI components after the endpoint and verb has been removed.
	 * Eg: /<endpoint>/<verb>/<arg0>/<arg1>/<arg2>
	*/
	protected $args = Array();
	/**
	 * Property: file
	 * Stores the input of the PUT requests (things sent to the API)
	 */
	protected $file = Null;
	/**
	 * Constructor __construct
	 * Allow for CROSS, assemble and pre-process the data
 	*/
 
 	protected $db;

 	public function __construct($request)
 	{
 		header("Access-Control-Allow-Origin: *");
 		header("Access-Control-Allow-Methods: *");
 		header("Content-Type: application/json");

 		// Get the args.
 		$this->args = explode('/', rtrim($request, '/'));

 		// Get the endpoint.
 		$this->endpoint = array_shift($this->args);

 		// Get the verb if it exists and is not a number.
 		/*if(array_key_exists(0, $this->args) && !is_numeric($this->args[0]))
 		{
 			$this->verb = array_shift($this->args);
 		}*/

 		// Get the method of the request.
 		$this->method = $_SERVER['REQUEST_METHOD'];
 		// Due to the way DELETE and PUT request are handled we have to get them from
 		// the header of the request.
 		if($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER))
 		{
 			if($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE')
 				$this->method = 'DELETE';
 			else if($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT')
 				$this->method = 'DELETE';
 			else
 				throw new Exception("Unexpected Header");
 		}

 		// Do things based on the method.
 		switch ($this->method) {
 			case 'DELETE':
 			case 'POST':
 				$this->request = $this->_cleanInputs($_POST);
 				break;

 			case 'GET':
 				$this->request = $this->_cleanInputs($_GET);
 				break;

			case 'PUT':
 				$this->request = $this->_cleanInputs($_GET);
 				$this->file = file_get_contents("php://input");
 				break;
 			
 			default:
 				$this->_response('Invalid Method', 405);
 				break;
 		}

 		// Connect to the database.
 		$this->db = DBConnect();
 	}

 	public function processAPI()
 	{
 		if(method_exists($this, $this->endpoint))
 		{
 			return $this->_response($this->{$this->endpoint}($this->args));
 		}
 		return $this->_response("No Endpoint $this->endpoint", 404);
 	}
 	protected function _response($data, $status = 200)
 	{
 		header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
 		return json_encode($data, JSON_PRETTY_PRINT);
 	}
 	private function _cleanInputs($data)
 	{
 		$cleanInput = Array();
 		if(is_array($data))
 		{
 			foreach ($data as $key => $value)
 			{
 				$cleanInput[$key] = $this->_cleanInputs($value);
 			}
 		}
 		else
 			$cleanInput = trim(strip_tags($data));
 		return
 			$cleanInput;
 	}

 	// Return the text version of each code.
 	private function _requestStatus($code)
 	{
 		$status = array(
 			200 => 'OK',
 			404 => 'Not Found',
 			405 => 'Method Not Allowed',
 			500 => 'Internal Server Error',
 		);
 		return ($status[$code])?$status[$code]:$status[500];
 	}

}
?>