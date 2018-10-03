<?php
/**
 * Browse Movies
 */
class browse_movies implements IPage
{
	private $header;
	function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$title = "Browse Movies";
		$leum->SetTitle($title);

		$menu = Front::GetWidget('menu', ['items'=>[
			['href' => ROOT . "/movies/add-new/", 'content' => 'Add Movie']
		]]);

		$this->header = Front::GetWidget('page_header', ['menu' => $menu, 'title' => $title]);
	}

	public function Content()
	{
		?>
		<div class="main">
			<?php $this->header->Show(); ?>
			<?php  ?>
		</div>
		<?php
	}
}
?>
