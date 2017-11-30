<?php
require_once 'api.class.php';

require_once 'mapping.php';
require_once 'tag.php';
require_once 'media.php';
require_once 'browse.php';

if(is_file('../../functions.php'))
	require_once '../../functions.php';

class LeumApi extends API
{
	public function Media ($args)
	{
		switch ($this->method)
		{
			case 'GET':
				if(isset($args[0]))
					return Media::Get($this->db, $args[0]);
				else
					return Media::Get($this->db, null);
				break;
			case 'DELETE':
				if(isset($args[0]))
					return Media::Delete($this->db, $args[0]);
				else
					throw new Exception("Invalid Arguments");
				break;
			//TODO: Create Put as well and separate the creation and modification of media.
			case 'POST':
				if(isset($args[0]))
					return Media::Post($this->db, $this->request, $args[0]);
				else
					return Media::Post($this->db, $this->request);
				break;
		}
	}
	public function Tag ($args)
	{
		switch ($this->method)
		{
			case 'GET':
				if(isset($args[0]))
					return Tag::Get($this->db, $args[0]);
				else
					return Tag::Get($this->db);
				break;
			case 'DELETE':
				if(isset($args[0]))
					return Tag::Delete($this->db, $args[0]);
				else
					throw new Exception("Invalid Arguments");
				break;
			case 'POST':
				if(isset($args[0]))
					return Tag::Insert($this->db, $this->request, $args[0]);
				else
					return Tag::Insert($this->db, $this->request);
				break;
		}
	}
	public function Find($args)
	{
		switch ($args[0])
		{
			case "tags":
					return Tag::FindTagsLike($this->db, $_GET['query']);
				break;
		}
	}
}


?>
