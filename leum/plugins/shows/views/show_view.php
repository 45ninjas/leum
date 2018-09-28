<?php
class ShowView
{
	public static function Tile($show)
	{
		?>
		<a class="item-tile" href="<?=ROOT . "/shows/$show->slug"?>">
			<img src="<?=$show->CoverUrl();?>">
		</a>
		<?php
	}
	public static function Full($show)
	{
		?>
		<div class="content">
			<h2><?=$show->title;?></h2>
			<?php foreach ($show->seasons as $season => $episodes)
			{
				self::Season($season, $episodes);
			}
			?>
		</div>
		<?php
	}
	public static function Season($season, $episodes)
	{
		?>
		<div class="season">
			<h3>Season <?=$season?></h3>
			<ul>
				<?php
				foreach ($episodes as $episode)
				{
					$link = ROOT . "/view/$episode->media";
					?>
					<a href="<?=$link?>"><li><span class="meta"><?="$episode->episode"?></span> <?=$episode->title?></li></a>
					<?php
				}
				?>
			</ul>
		</div>
		<?php
	}

	public static function EditForm($show)
	{
		if(isset($show->title))
			$title = " value=\"$show->title\" ";
		else
			$title = "";

		if(isset($show->slug))
			$slug = " value=\"$show->slug\" ";
		else
			$slug = "";

		if(isset($show->cover_image))
			$cover_image = " value=\"$show->cover_image\" ";
		else
			$cover_image = "";
		?>
		<div class="content">
			<form class="pure-form pure-form-stacked" method="POST">
				<?php if(isset($show->id)) : ?>
				<input type="hidden" name="id" value="<?=$show->id?>">
				<?php endif; ?>
				<label for="title">Title</label>
				<input class="pure-input" type="text" name="title" id="title"<?=$title?>>

				<label for="slug">Slug</label>
				<input class="pure-input" type="text" name="slug" id="slug"<?=$slug?>>
				
				<label for="description">Description</label>
				<textarea class="pure-input" type="text" name="description" id="description"><?php if(isset($shows->description)) echo $shows->description; ?></textarea> 
				
				<label for="cover_image">Cover Image</label>
				<input class="pure-input" type="text" name="cover_image" id="cover_image"<?=$cover_image?>>

				<input class="pure-button pure-button-primary" type="submit" name="set-media">
			</form>
		</div>
		<?php
	}
}
?>