<?php
/**
* 404 Page
*/
class Error404 extends Page
{
	public $title = "404 - File not found.";
	protected $message;
	public function __construct($arguments)
	{
		http_response_code(404);

		if(isset($arguments[0]) && is_string($arguments[0]))
			$this->message = $arguments[0];
	}
	public function Content()
	{?>
<div class="main">
	<div class="header">
		<h1>404 - Uh-Oh!</h1>
		<h2>Yep, you read it correctly, it's a 404 alright.</h2>
	</div>

	<div class="content">
		<?php if(isset($this->message)): ?><h2 class="content-subhead"><?=$this->message;?></h2><?php endif; ?>
		<h2 class="content-subhead">Things that could have gone wrong</h2>
			<ul>
				<li>A file has been moved.</li>
				<li>The database has been modified without your or it's consent.</li>
				<li>You typed the wrong URL, go check it.</li>
				<li>The server's media directory has been configured incorrectly.</li>
				<li>Symlinks to the media directory have failed.</li>
				<li>The world ended.</li>
			</ul>
			<p>But seriously it's probably your fault.</p>
		<img class="pure-img" src="<?php asset("/resources/graphics/yotsuba-kowai-paint.png") ?>">
	</div>
</div>
<?php }
}
?>
