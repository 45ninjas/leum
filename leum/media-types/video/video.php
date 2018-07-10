<?php 
namespace "types";
class video
{
	public function Preview($mediaItem)
	{
		?>
		<video class="leum-content-item" controls muted src="<?php echo $mediaItem->GetLink(); ?>">
			<?php $this->NoSupport(); ?>
		</video>
		<?php
	}

	public function View($mediaItem)
	{
		?>
		<video class="leum-content-item" controls autoplay src="<?php echo $mediaItem->GetLink(); ?>">
			<?php $this->NoSupport(); ?>
		</video>
		<?php
	}

	private function NoSupport()
	{
		?>
		<div class="leum-bad-type">
			<span class="fa-stack fa-3x">
				<i aria-hidden="true" class="fa fa-file-o fa-stack-2x"></i>
				<i aria-hidden="true" class="fa fa-exclamation-triangle fa-stack-1x"></i>
			</span>
			<h3>No Video Support.</h3>
			<p>Looks like you are using a browser that does not support this video format.</p>
		</div>
		<?php
	}

	public function CreateThumbnail($source, $thumbnail)
	{
		// TODO: Create the thumbnail generation code here.
	}

}
 ?>