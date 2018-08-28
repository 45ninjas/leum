<?php
/**
* View individual pages.
*/
require_once SYS_ROOT . '/core/leum-core.php';

class tv_shows implements IPage
{
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{

	}
	public function Content()
	{ ?>

	<div class="main">
		<div class="header">
			<div class="content">
				<h1>TV Shows</h1>
				<!-- Filter Box Goes Here-->
			</div>
		</div>
		<div class="content">
			<p><?="22 shows exist."?></p>
		</div>
		<div class="items">
			<div class="show">
				<h3>My Show Name</h3>
				<p>4 Seasons, 48 Episodes</p>
			</div>
		</div>
		<div class="content">
			<!-- Page Buttons Goes Here -->
		</div>
	</div>

<?php }
}
?>
