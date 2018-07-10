<?php 
namespace "types";
class Image
{
	public function Preview($mediaItem)
	{
		$this->View($mediaItem);
	}

	public function View($mediaItem)
	{
		?>
		<img class="leum-content-item" src="<?php echo $mediaItem->GetLink(); ?>">
		<?php
	}
	public function CreateThumbnail($source, $thumbnail)
	{
		$image = ImageCreateFromImage($source);

		$size = getimagesize($source);
	}
}
 ?>