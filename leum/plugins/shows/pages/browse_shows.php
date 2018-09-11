<?php
/**
* Shows Page
*/
// require_once SYS_ROOT . "/leum/core/leum-core.php";
require_once SYS_ROOT . '/leum/plugins/shows/views/show_view.php';

class browse_shows implements IPage
{
	private $shows;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$leum->SetTitle("Shows");
		if(isset($arguments[0]))
		{
			$this->shows = Show::Get($dbc, $arguments[0]);
			if($this->shows)
			{
				$leum->SetTitle($this->shows->title);
				$this->shows->GetEpisodes($dbc);
			}
			else
				$leum->Show404Page();
		}
		else
			$this->shows = Show::Get($dbc);
	}
	public function Content()
	{ ?>
<div class="main">
	<div class="header">
		<div class="content">
			<h1>Shows</h1>
			<div class="pure-menu pure-menu-horizontal">
				<ul class="pure-menu">
				</ul>
			</div>
		</div>
	</div>
		<?php
	if(!isset($this->shows) || count($this->shows) == 0)
		echo '<div class="content"><p>0 shows where found.</p></div>';
	else if($this->shows instanceof Show)
	{
		ShowView::Full($this->shows);
		// var_dump($this->shows);
	}
	else
	{
		echo "<div class=\"items\">";
		foreach ($this->shows as $show)
		{
			ShowView::Tile($show);
		}
		echo "</div>";
	}
	?>
</div>

<?php
	}
}
?>