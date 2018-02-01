<?php 
namespace "MediaTypes";
/**
* Base type class
*/
class Video implements MediaType
{
	public function DoPreview($mediaItem)
	{
		$this->DoView($mediaItem);
	}

	public function DoView($mediaItem)
	{
		?>
		<video class="leum-content-item" controls src="<?php echo $mediaItem->GetLink(); ?>">
			<div class="leum-bad-type">
				<span class="fa-stack fa-3x">
					<i aria-hidden="true" class="fa fa-file-o fa-stack-2x"></i>
					<i aria-hidden="true" class="fa fa-exclamation-triangle fa-stack-1x"></i>
				</span>
				<h3>No Video Support.</h3>
				<p>Looks like you are using a browser that does not support this video format.</p>
			</div>
		</video>
		<?php
	}

	public function CreateThumbnail($source, $thumbnail)
	{

	}

}
 ?>