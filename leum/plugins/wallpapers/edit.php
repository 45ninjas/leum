<?php
class edit implements IPage
{
	private $title;
	private $wallpapers;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$this->title = "Wallpapers";
		$leum->SetTitle($this->title);
		$this->wallpapers = Wallpapers::$instance->GetAllWallpapers(LeumCore::$dbc);
	}
	public function Content()
	{ ?>
<div class="main">
	<div class="header">
		<div class="content">
			<h1><?=$this->title?></h1>
<!-- 			<div class="pure-menu pure-menu-horizontal">
				<ul class="pure-menu">

				</ul>
			</div> -->
		</div>
	</div>
	
	<div class="content">
		<p>To add more wallpapers go to a media item and set the type to 'wallpaper'.</p>
		<?php $this->ShowWallpapers(); ?>
	</div>
</div>

<?php
	}

	function ShowWallpapers()
	{
		foreach ($this->wallpapers as $wallpaper)
		{
			?>
			<img class="image" src="<?=$wallpaper['link']?>">
			<?php
		}
	}

}
?>