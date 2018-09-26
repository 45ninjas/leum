<?php

require_once SYS_ROOT . "/leum/utils/thumbnails.php";

class ImportUtility
{
	public $data;
	public function ImportFile($file)
	{
		$this->data = json_decode(file_get_contents($file), true);

		if(!isset($this->data['export']))
			throw new Exception("Import file does not have any 'export' data");
	
		$exportData = $this->data['export'];

		// Create some warnings if the export data is different.
		// Check the LeumCore versions.
		if($exportData['leum version'] != LeumCore::VERSION)
			Message::Create("warning", "Version mismatch in import file. LeumCore: " . LeumCore::VERSION . " Version in file: " . $exportData['leum version'], "importer");

		// Check the importer versions.
		if($exportData['importer version'] != Importer::VERSION)
			Message::Create("warning", "Version mismatch in import file. Importer: " . Importer::VERSION . " Version in file: " . $exportData['importer version'], "importer");

		// Check if any plugins are no longer installed.
		if(count(array_diff($exportData['plugins'], ACTIVE_PLUGINS)) > 0)
			Message::Create("warning", "The import file was created using plugins that are not active. Some data might not be imported.", "importer");

		// Looks good, let's import it.
		$this->CoreImport();

		// Let the other plugins know there is data to import.
		LeumCore::InvokeHook("importer.import", $this);
	}
	public function CoreImport()
	{
		foreach ($this->data['media'] as $mediaItem)
		{
			// Make sure the file exists.
			if(!is_file(SYS_ROOT . MEDIA_DIR . "/" . $mediaItem['file']))
			{
				Message::Create("warning", $mediaItem['file'] ." does not exist. Not importing.", "importer");
				continue;
			}
			// Make sure the file has not already been imported.
			if($this->AlreadyImported($mediaItem['file']) != false)
			{
				Message::Create("warning", $mediaItem['file'] ." already exists, Not importing.", "importer");
				continue;
			}

			// Create the media item.
			$media = Media::InsertSingle(LeumCore::$dbc, $mediaItem);

			// Make sure the media item was actually created.
			if($media == false)
			{
				Message::Create("warning", $mediaItem['file'] ." failed to create the media item.", "importer");
				continue;
			}

			// Assign the tags.
			TagMap::SetMappedTags(LeumCore::$dbc, $media, $mediaItem['tags'], true);

			// Create the thumbnail.
			try
			{
				$thumbnail = Thumbnails::MakeFor(LeumCore::$dbc, $media, false);
			}
			catch (Exception $e)
			{
				Message::Create("error", "Unable to create thumbnail. Exception: $e", "importer");
				continue;
			}

				Message::Create("success", "Imported " . $mediaItem['title'], "importer");

		}
	}

	private $path;
	public $description;
	public function ImportDirectory($dir, $chunkSize = 20)
	{
		// Remove the trailing slash from the path and add it again? That's stupid.
		$this->path = rtrim(SYS_ROOT . MEDIA_DIR, '/') . '/';

		// Bail out if the directory does not exist.
		if(!is_dir($this->path . $dir))
			throw new Exception("Import directory does not exist");
			
		// Create the iterators.
		$dirIterator = new RecursiveDirectoryIterator($this->path . $dir);
		$iterator = new RecursiveIteratorIterator($dirIterator);

		// iterate.
		$successItems = [];
		$iterator->Rewind();
		while ($iterator->valid())
		{
			$current = $iterator->current();

			// Only attempt to import files.
			if($current->isFile())
			{
				$import = $this->Import($current, $localPath, $thumbnail);

				if($import != false)
				{
					$successItems[] = [
						"id" => $import,
						"path" => $localPath,
						"thumbnail" => substr($thumbnail, strlen($this->path))
					];
				}
			}

			// Next.
			$iterator->next();
		}
		return $successItems;
	}

	function Import($info, &$localPath, &$thumbnail)
	{
		// Chop off the SYS_ROOT . MEDIA_DIR . '/' part of the string.
		$localPath = substr($info->getPathname(), strlen($this->path));

		if($this->AlreadyImported($localPath) != false)
		{
			Message::Create("warning", "$localPath already exists, Not importing", "importer");
			return false;
		}

		$data = [
			'title'			=> $info->getBasename(),
			'description'	=> $this->description,
			'file'			=> $localPath
		];

		$media = Media::InsertSingle(LeumCore::$dbc, $data);

		if($media == false)
		{
			Message::Create("error", "An error was encountered while importing $localPath.", "importer");
			return false;
		}
		else
		{
			try
			{
				$thumbnail = Thumbnails::MakeFor(LeumCore::$dbc, $media);
			}
			catch (Exception $e)
			{
				Message::Create("error", "Unable to create thumbnail. Exception: $e", "importer");
				return false;
			}
		}
		return $media;
	}
	function AlreadyImported($file)
	{
		$sql = "SELECT 1 from media where file = ? limit 1";
		$statement = LeumCore::$dbc->prepare($sql);
		$statement->execute([$file]);

		return $statement->fetch();
	}
}

?>
