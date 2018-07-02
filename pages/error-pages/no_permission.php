<?php
/**
* No Permissions Page
*/
class no_permission implements IPage
{
	private $message;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$message = $arguments['error-message'];
	}

	public function Content()
	{?>
<div class="main">
	<div class="header">
		<h1>NO!</h1>
		<h2>You don't have permission to do that.</h2>
	</div>

	<div class="content">
		<?php if(isset($this->message)): ?><h2 class="content-subhead"><?=$this->message;?></h2><?php endif; ?>
		<img class="pure-img" src="<?php asset("/resources/graphics/yotsuba-kowai-paint.png") ?>">
	</div>
</div>
<?php }
}
?>
