<?php namespace API;
use Tag as CoreTag;
class Tags
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
			// Get all the paginated
			$data = array();

			$tags = CoreTag::GetAll($this->dbc);

			// Get the count of total items.
			$totalItems = $this->dbc->query("SELECT found_rows()")->fetch()["found_rows()"];
			$data['total'] = $totalItems;

			// Finally, return the stuff.
			$data['tags'] = $tags;
			return $data;
		}
		// Return only one tags item because the first argument is a number.
		else if($this->IsArgNumber())
		{
			$index = $this->api->args[0];
			if(!is_numeric($index) || $index < 0)
				$data = CoreTag::GetSingle($this->dbc, $index);

			if($data == null)
				throw new \Exception("tags not found");
		}
		else
			throw $this->inputException;

		return $data;
	}
	public function Post()
	{
		$data = $this->api->data;
		
		$Tag = new CoreTag();
		if(isset($data['title']))
			$Tag->title = $data['title'];
		else
			throw new \Exception("Title was not defined");
		
		if(isset($data['slug']))
			$Tag->slug = $data['slug'];

		if($this->IsArgNumber())
		{
			$index = $this->api->args[0];
			$Tag->tag_id = $this->api->args[0];
			return (int)CoreTag::InsertSingle($this->dbc, $Tag);
		}
		else
			return (int)CoreTag::InsertSingle($this->dbc, $Tag);
	}
	public function Delete()
	{
		if($this->IsArgNumber())
		{
			$index = $this->api->args[0];
			return (int)CoreTag::DeleteSingle($this->dbc, $index);
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