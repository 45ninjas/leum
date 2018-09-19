<?php
/**
* No Permissions Page
*/
class no_permission implements IPage
{
	private $message;
	private $permission;
	private $header;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$this->message = $arguments['error-message'];
		$this->permission = $arguments['error-permission'];

		$args = [
			'title'		=> "No!",
			'subtitle'	=> "You don't have permission to do that."
		];
		$this->header = Front::GetWidget('page_header', $args);
	}

	public function Content()
	{
	?>
	<div class="main">
		<?php
		if($this->header != false)
			$this->header->Show();
		?>
		<div class="content">
			<?php if(isset($this->message)): ?><h3 class="content-subhead"><?=$this->message;?></h3><?php endif; ?>
			<p>To access this page your account requires the following permissions: <?=$this->permission?>.</p>
			<img class="pure-img middle" src="<?php asset("/resources/graphics/yotsuba-block.jpg") ?>">
		</div>
	</div>
</div>
<?php }
}
?>
