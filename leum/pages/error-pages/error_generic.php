<?php
/**
* Generic Error Page
*/
class error_generic implements IPage
{
	private $message;
	private $code;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$this->message = $arguments['error-message'];
		$this->code = $arguments['error-code'];
	}

	public function Content()
	{?>
<div class="main">
	<div class="header">
		<div class="content">
			<h1><?=$this->code?></h1>
			<h2>Looks like there is an issue.</h2>
		</div>
	</div>

	<div class="content">
		<?php if(isset($this->message)): ?><h2 class="content-subhead"><?=$this->message;?></h2><?php endif; ?>
		<img class="pure-img middle" src="<?php asset("/resources/graphics/yotsuba-kowai-paint.png") ?>">
	</div>
</div>
<?php }
}
?>
