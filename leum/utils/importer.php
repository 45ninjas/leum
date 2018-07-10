<?php
require_once "thumbnails.php";
function ImportDirectory($mediaDirectory, $dbc)
{
	$path = SYS_ROOT . MEDIA_DIR;

	$path = rtrim($path, '/') . '/';

	$directory = new RecursiveDirectoryIterator($path . $mediaDirectory);
	$iterator = new RecursiveIteratorIterator($directory);

	foreach($iterator as $info)
	{
		if(!$info->isFile())
			continue;
		
		$localPath = substr($info->getPathname(), strlen($path));

		$items = Media::GetFromPath($dbc, $localPath);

		if(count($items) == 0)
		{
			$data = array();
			$data['title'] = $info->getFilename();
			$data['source'] = "Leum automatic media importer.";
			$data['path'] = $localPath;
			$media = Media::InsertSingle($dbc, $data);

			if($media == false)
			{
				Message::Create("msg-error", "Error while adding $localPath to the database", "importer");
			}
			else
			{
				try
				{
					// Create the thumbnail.
					Thumbnails::MakeFor($dbc, $media);	
				}
				catch (Exception $e)
				{
					Message::Create("msg-red", "Unable to create thumbnail.<br>Exception: $e");
				}
				Message::Create("msg-green", "Imported $localPath successfully.", "importer");
			}
		}
		else
		{
			Message::Create("msg", "$localPath already exists, skipping.", "importer");
		}
	}
}
?>