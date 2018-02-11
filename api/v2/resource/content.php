<?php namespace API;
use Media as CoreMedia;
class Content
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

			// Get the page index provided in the input data.
			if(isset($this->api->data['page']) && is_numeric($this->api->data['page']))
				$data['page'] = $this->api->data['page'];
			else
				$data['page'] = 0;

			// Get the page size provided in the input data.
			if(isset($this->api->data['size']) && is_numeric($this->api->data['size']))
				$data['page size'] = $this->api->data['size'];
			else
				$data['page size'] = PAGE_SIZE;

			// Get the items for this the page.
			$mediaItems = CoreMedia::GetAll($this->dbc, $data['page'], $data['page size']);

			// Get the count of total items.
			$totalItems = $this->dbc->query("SELECT found_rows()")->fetch()["found_rows()"];
			$data['total'] = $totalItems;

			// Finally, return the stuff.
			$data['media'] = $mediaItems;
			return $data;
		}
		else
			throw $this->inputException;

		return $data;
	}
}
 ?>