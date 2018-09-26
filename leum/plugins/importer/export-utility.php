<?php

/**
 * Exporter exports data.
 */
class ExportUtility
{
	private $data;
	private $file;
	function __construct($file)
	{
		$this->data = ['export' => [
			'leum version' => LeumCore::VERSION,
			'importer version' => Importer::VERSION,
			'plugins' => ACTIVE_PLUGINS
		]];
		$this->file = $file;

		if(!is_file($file))
			touch($file);
		
		if(!is_file($file))
			throw new Exception("Unable to create the backup file");
	}

	function ExportLeum()
	{
		$this->CoreExport();
		// Get all the other plugins to export if they want.
		LeumCore::InvokeHook('importer.export', $this);

		$json = json_encode($this->data, JSON_PRETTY_PRINT);

		file_put_contents($this->file, $json);
	}
	function CoreExport()
	{
		$sql = "SELECT *, (SELECT DISTINCT GROUP_CONCAT(t.slug)
			from tags t
			join tag_map m on m.tag = t.id
			where m.media = media.id) as tags
			from media;";
		$statement = LeumCore::$dbc->query($sql);

		$result = array();

		while($row = $statement->fetch(PDO::FETCH_ASSOC))
		{
			$row['tags'] = explode(',', $row['tags']);

			array_push($result, $row);
		}

		$this->data['media'] = $result;
	}
	function AddData($key, $data)
	{
		$this->data[$key] = $data;
	}
}

?>