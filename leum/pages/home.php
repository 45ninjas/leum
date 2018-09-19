<?php
/**
* Home Page
*/
class home implements IPage
{
	private $header;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$leum->SetTitle("Home");

		$menuArgs = ['items' => [
			['content' => "Browse", "href" => ROOT . "/browse"],
			['content' => "Edit", "href" => ROOT . "/edit"],
			['content' => "GitHub", "href" => ROOT . "https://github.com/Those45Ninjas/leum"],
			]];

		$menu = Front::GetWidget('menu', $menuArgs);

		$this->header = Front::GetWidget('page_header', [
			"title" => APP_TITLE,
			"subtitle" => "A boring old homepage",
			"menu" => $menu
		]);

	}
	public function Content()
	{
		?>
<div class="main">

	<?php if(isset($this->header)) $this->header->Show(); ?>

	<div class="content">
		<p>View this project on <a href="https://github.com/Those45Ninjas/leum">GitHub</a>. Read the docs at <a href="https://leum.readthedocs.io/en/latest/">Read the Docs</a>.</p>
		<h2 class="content-subhead">Project Requirements</h2>
		<p>This project needs to fulfil the following requirements to be successful.</p>
		<ul>
			<li>Easy to use RESTful API that supports json or directly with php.</li>
			<li>Model View Controller model that's simple, easy to understand and functional.</li>
			<li>Many to Many relationship between media and tags</li>
			<li>Easy to modify.</li>
			<li>Support editing multiple files at once.</li>
			<li>Generate thumbnails.</li>
		</ul>
	</div>
</div>
		<?php
	}
}
?>