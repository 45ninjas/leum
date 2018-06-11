<?php namespace API;
use Media as CoreMedia;
use Mapping as CoreMapping;
class Media
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
			$totalItems = \LeumCore::GetTotalItems($this->dbc);
			$data['total'] = $totalItems;

			// Finally, return the stuff.
			$data['media'] = $mediaItems;
			return $data;
		}
		// Return only one media item because the first argument is a number.
		else if($this->IsArgNumber())
		{
			$index = $this->api->args[0];
			$data = CoreMedia::GetSingle($this->dbc, $index);

			if(isset($this->api->data['usage']) && $this->api->data['usage'] === "viewer")
					return $this->GetViewer($data);

			if(!isset($data))
				throw new \Exception("Media not found");

			// $data->tags = $data->GetTags($dbc, true);
		}
		else
			throw $this->inputException;

		return $data;
	}
	public function GetViewer($media)
	{
		if(!isset($media))
			return false;

		$response["title"] = $media->title;
		$response["edit link"] = ROOT . "/edit/media/$media->media_id";
		$response["id"] = $media->media_id;
		$response["tags"] = $media->GetTags($this->dbc, true);

		require_once SYS_ROOT . "/page-parts/media-viewer.php";
		$viewer = new \MediaViewer($media, false, true, true, true, true);
		ob_start();
		$viewer->ShowContent();
		$response["content"] = trim(ob_get_clean());

		return $response;
	}
	public function Post()
	{
		$data = $this->api->data;

		$mediaItem = new CoreMedia();
		if(isset($data['title']) && isset($data['path']) && isset($data['source']))
		{
			$mediaItem->title = $data['title'];
			$mediaItem->path = $data['path'];
			$mediaItem->source = $data['source'];
		}

		if($this->IsArgNumber())
		{
			$mediaItem->media_id = $this->api->args[0];
			if(isset($data['set-tags']))
			{
				$newSlugs = explode(',', $data['set-tags']);
				$addNew = $data['add-new'] == true;
				return CoreMapping::SetMappedTags($this->dbc,$mediaItem, $newSlugs, $addNew);
			}
			else
			{
				
				return (int)CoreMedia::InsertSingle($this->dbc, $mediaItem);
			}
		}
		else
			return (int)CoreMedia::InsertSingle($this->dbc, $mediaItem);
	}
	public function Delete()
	{
		if($this->IsArgNumber())
		{
			$index = $this->api->args[0];
			return (int)CoreMedia::DeleteSingle($this->dbc, $index);
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