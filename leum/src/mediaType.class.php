<?php 
/**
* Base type class
*/
interface MediaType
{
	public function DoPreview($mediaItem);
	public function DoView($mediaItem);
	public function CreateThumbnail($source, $thumbnail);
}
 ?>