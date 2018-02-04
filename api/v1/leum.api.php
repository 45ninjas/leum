<?php
if(!defined('SYS_ROOT'))
	define('SYS_ROOT', realpath(__DIR__ . "/../.."));

// Old Leum API includes
require_once SYS_ROOT . "/api/v1/api.class.php";

require_once SYS_ROOT . "/api/v1/mapping.php";
require_once SYS_ROOT . "/api/v1/browse.php";
require_once SYS_ROOT . "/api/v1/ingest.php";

require_once SYS_ROOT . "/functions.php";
require_once SYS_ROOT . "/prefrences.php";

// Newer Leum Core Includes.
require_once SYS_ROOT . "/core/media.php";
require_once SYS_ROOT . "/core/tag.php";
// require_once SYS_ROOT . "/core/mapper.php";

class LeumApi extends API
{
	public function Media ($args)
	{
		switch ($this->method)
		{
			case 'GET':
				if(isset($args[0]))
					return Media::GetSingle($this->db, $args[0]);
				else
					return Media::GetAll($this->db, null);
				break;
			case 'DELETE':
				if(isset($args[0]))
					return Media::DeleteSingle($this->db, $args[0]);
				else
					throw new Exception("Invalid Arguments");
				break;
			//TODO: Create Put as well and separate the creation and modification of media.
			case 'POST':
				if(isset($args[0]))
					return Media::InsertSingle($this->db, $this->request, $args[0]);
				else
					return Media::InsertSingle($this->db, $this->request);
				break;
		}
	}
	public function Tag ($args)
	{
		switch ($this->method)
		{
			case 'GET':
				if(isset($args[0]))
					return Tag::GetSingle($this->db, $args[0]);
				else
					return Tag::GetAll($this->db);
				break;
			case 'DELETE':
				if(isset($args[0]))
					return Tag::DeleteSingle($this->db, $args[0]);
				else
					throw new Exception("Invalid Arguments");
				break;
			case 'POST':
				if(isset($args[0]))
					return Tag::InsertSingle($this->db, $this->request, $args[0]);
				else
					return Tag::InsertSingle($this->db, $this->request);
				break;
		}
	}
	public function Find($args)
	{
		switch ($args[0])
		{
			case "tags":
					return Tag::FindLike($this->db, $_GET['query']);
				break;
		}
	}
	public function Ingest($args)
	{
		switch ($args[0]) {
			case 'process':
					return Ingest::Process($this->db, $args[1]);
				break;
		}
	}
	public function Browse($args)
	{
		switch ($args[0]) {
			case 'media-modal':
					return Browse::GetModalItem($this->db, $args[1]);
				break;
		}
	}
}


?>
