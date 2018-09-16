<?php

require_once SYS_ROOT . "/leum/core/leum-core.php";
require_once SYS_ROOT . "/leum/functions.php";

class Thumbnails
{
	public static function MakeForMultiple($dbc, $mediaItems, $override = false)
	{
		$results = array(
			'skipped'		=> 0,
			'failed'		=> 0,
			'success'		=> 0,
		);
		foreach ($mediaItems as $item)
		{	
			try
			{
				if($override || !is_file($item->GetThumbPath()))
				{
					self::MakeFor($dbc, $item);
					$results["success"] ++;
				}
				else
					$results["skipped"] ++;
			}
			catch( Exception $e)
			{
				$results["failed"] ++;
				echo "Failed to process $item->id. ".$e->getMessage()."\n";
			}
		}
		return $results;
	}
	public static function MakeFor($dbc, $media)
	{
		// Get the media item's path;
		if(!$media instanceof Media)
		{
			$media = Media::GetSingle($dbc, $media);
			if(!isset($media))
			return false;
		}
		$path = $media->GetPath();

		$targetFile = $media->GetThumbPath();

		if(!is_dir(dirname($targetFile)))
			mkdir(dirname($targetFile), 0770, true);

		$baseType = explode('/', $media->GetMimeType())[0];

		// If it's an image create a thumbnail from the image.
		if($baseType === "image")
			self::CreateThumbnailFromImg($media->GetPath(), $targetFile);

		// If it's a video create a thumbnail from the video.
		else if($baseType === "video")
		{
			//$tmp = tmpfile();
			$tmp = SYS_ROOT . THUMB_DIR . "/tmp/ffmpeg-working.png";
			
			if(!is_dir(dirname($tmp)))
				mkdir(dirname($tmp), 0770, true);

			self::SnapshotVideo($media->GetPath(), $tmp);
			self::CreateThumbnailFromImg($tmp, $targetFile);

			unlink($tmp);
		}
	}
	public static function SnapshotVideo($input, $output, $percentage = 0.4)
	{

		$input = escapeshellarg($input);
		$output = escapeshellarg($output);

		// The the duration of the media.
		$ffprobe = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $input";
		exec($ffprobe, $ffprobeOut, $exit_code);

		if($exit_code)
			throw new Exception("FFprobe Error, " . implode('\n\r', $ffprobeOut));
		$exit_code = 0;

		$duration = (float)$ffprobeOut[0];
		if(!is_numeric($duration))
		{
			var_dump($ffprobeOut);
			throw new Exception("Unexpected output from FFprobe");
		}
		$time = escapeshellarg($duration * $percentage); 

		// Actually get the picture
		$command = "ffmpeg -ss $time -i $input -vframes 1 -vcodec png -y $output";
		exec($command, $ffmpegOut, $exit_code);

		if($exit_code)
			throw new Exception("FFmpeg Error, " . implode('\n\r', $ffmpegOut));

		return true;		
	}
	public static function SnapshotAudio($input, $output, $waveform = false)
	{
		
	}
	private static function CreateThumbnailFromImg($input, $output)
	{
		// Check the file actually exists.
		if(!is_file($input))
			throw new Exception("Input '$input' does not exist.");

		// Get some information about the image and create an image from this information.
		$imgInfo = getimagesize($input);
		$image = self::ImageCreateFrom($input, $imgInfo);

		// Set the width and height to the values in imgInfo.
		list($width, $height) = $imgInfo;

		// Figure out where we should resample.
		if($width > $height)
		{
			$x = ($width - $height) / 2;
			$y = 0;
			$smallestSide = $height;
		}
		else
		{
			$x = 0;
			$y = ($height - $width) / 2;
			$smallestSide = $width;
		}

		// Create a new output image / buffer.
		$thumbnail = imagecreatetruecolor(THUMB_SIZE, THUMB_SIZE);

		// Resample the image into a thumbnail.
		if(!imagecopyresampled(
			$thumbnail,						// Destination
			$image,							// Source
			// X 				Y
			0,				0,				// Destination position
			$x,				$y,				// Source position
			THUMB_SIZE,		THUMB_SIZE,		// Destination size
			$smallestSide,	$smallestSide	// Source size
		))
			throw new Exception("Error Processing Image, cropping failed.");

		// destroy the old image, write the new image and destroy the new image.
		imagedestroy($image);
		if(!imagejpeg($thumbnail, $output))
			throw new Exception("Error Writing Image");
		imagedestroy($thumbnail);

		// Return true to let the script know we fished successfully.
		return true;
	}
	private static function ImageCreateFrom($input, $size = null)
	{
		if(!is_file($input))
			throw new Exception("Input '$input' does not exist.");

		if(!isset($size))
			$size = getimagesize($input);

		if($size == false)
			throw new Exception("Error Processing Image, getimagesize failed.");

		switch ($size[2])
		{
			case IMAGETYPE_GIF:
				return imagecreatefromgif($input);
			case IMAGETYPE_JPEG:
				return imagecreatefromjpeg($input);
			case IMAGETYPE_PNG:
				return imagecreatefrompng($input);
			case IMAGETYPE_BMP:
				return imagecreatefrombmp($input);
			case IMAGETYPE_WEBP:
				return imagecreatefromwebp($input);
			default:
				throw new Exception("Error Processing Request, image format not supported");
		}
	}
}
?>