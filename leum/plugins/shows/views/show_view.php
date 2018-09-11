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
}
?>