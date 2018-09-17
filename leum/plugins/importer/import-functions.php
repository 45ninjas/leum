<?php

require_once SYS_ROOT . "/leum/utils/thumbnails.php";

class ImportUtility
{
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
			Message::Create("warning", "$localPath already exists, skipping", "importer");
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
			Message::Create("error", "An error was encountered while importing $localPath", "importer");
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
