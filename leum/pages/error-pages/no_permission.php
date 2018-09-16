<?php
/**
* No Permissions Page
*/
class no_permission implements IPage
{
	private $message;
	private $permission;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$this->message = $arguments['error-message'];
		$this->permission = $arguments['error-permission'];
	}

	public function Content()
	{?>
<div class="main">
	<div class="header">
		<div class="content">
			<h1>NO!</h1>
			<h2>You don't have permission to do that.</h2>
		</div>
	</div>

	<div class="content">
		<?php if(isset($this->message)): ?><h2 class="content-subhead"><?=$this->message;?></h2><?php endif; ?>
		<p>To access this page your account requires the following permissions: <?=$this->permission?>.</p>
		<img class="pure-img middle" src="<?php asset("/resources/graphics/yotsuba-block.jpg") ?>">
	</div>
</div>
<?php }
}
?>
