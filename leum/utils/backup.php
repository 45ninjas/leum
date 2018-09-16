<?php
if(!defined('SYS_ROOT'))
	define('SYS_ROOT', realpath(__DIR__ . '/../../'));

require_once SYS_ROOT . "/leum/core/leum-core.php";

$backup = new Backup();
$backup->ExportMedia();

class Backup
{
	public $leumCore;
	public $dbc;
	public function __construct()
	{
		$this->leumCore = new LeumCore();
		if(isset(LeumCore::$dbc))
			$this->dbc = LeumCore::$dbc;
		else
			$this->dbc = DBConnect();
	}

	public function ExportMedia()
	{
		$sql = "SELECT *,
		(SELECT DISTINCT GROUP_CONCAT(t.slug)
			from tags t
			join tag_map m on m.tag = t.tag_id
			where m.media = media.media_id) as tags
		from media;";

		$statement = $this->dbc->query($sql);

		$data = array();

		while($row = $statement->fetch(PDO::FETCH_ASSOC))
		{

			$item['title'] = $row['title'];
			$item['description'] = $row['description'];
			$item['file'] = $row['file'];
			$item['date'] = $row['date'];
			$item['tags'] = explode(',', $row['tags']);

			array_push($data, $item);
		}

		echo json_encode($data, JSON_PRETTY_PRINT);
	}

	public function ImportMedia()
	{
		throw new Exception("Importing is not implemented yet. Feel free to change that.");
		
	}
}

?>