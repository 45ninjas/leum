<?php namespace API;
use User as CoreUser;
class User
{
	private $dbc;
	private $invalidInput;
	function __construct($api)
	{
		$this->inputException = new \Exception("Invalid Input(s), Index needs to be a number grater or equal to zero");
		$this->api = $api;
		$this->dbc = $api->dbc;
	}
	public function GET()
	{
		if(count($this->api->args) == 0)
		{
			// Get all the media paginated
			$data = array();

			// Get the items for this the page.
			$users = CoreUser::GetAll($this->dbc);

			// Get the count of total items.
			$totalItems = \LeumCore::GetTotalItems($this->dbc);
			$data['total'] = $totalItems;

			// Finally, return the stuff.
			$data['users'] = $users;
			return $data;
		}
		// Return only one media item because the first argument is a number.
		else if($this->IsArgNumber())
		{
			$index = $this->api->args[0];
			$data = CoreUsers::GetSingle($this->dbc, $index);

			if(!isset($data))
				throw new \Exception("Media not found");
		}
		else
			throw $this->inputException;

		return $data;
	}
	public function Delete()
	{
		if($this->IsArgNumber())
		{
			$index = $this->api->args[0];
			return (int)CoreUser::DeleteSingle($this->dbc, $index);
		}
		
		throw $this->inputException;
	}
	function IsArgNumber($index = 0)
	{
		if(count($this->api->args) > 0)
		{
			$arg = $this->api->args[$index];
			return isset($arg) && is_numeric($arg) && $arg > 0;
		}
		return false;
	}
}
 ?>