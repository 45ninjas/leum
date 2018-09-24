<?php
/**
* Generic Error Page
*/
class error_generic implements IPage
{
	private $message;
	private $code;
	private $header;
	private $content;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		$this->message = $arguments['error-message'];
		$this->code = $arguments['error-code'];

		if(isset($arguments['error-content']))
			$this->content = $arguments['error-content'];

		$leum->SetTitle($this->code);

		$args = [
			'title'		=> $this->code,
			'subtitle'	=> $this->message
		];
		$this->header = Front::GetWidget('page_header', $args);
	}

	public function Content()
	{?>
<div class="main">
	<?php
		if($this->header != false)
			$this->header->Show();
	?>
	<div class="content">
		<?php Message::ShowMessages("exception"); ?>
		<?php if(isset($this->content)) echo $this->content; ?>
		<img class="pure-img middle" src="<?php asset("/resources/graphics/yotsuba-munch.png") ?>">
	</div>
</div>
<?php }
}
?>
