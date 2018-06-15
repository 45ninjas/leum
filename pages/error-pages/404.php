<?php
/**
* 404 Page
*/
class Page
{
	protected $message;
	protected $code = 404;
	public function __construct($leum, $dbc, $userInfo, $arguments)
	{
		// Get the error message from the arguments.
		if(isset($arguments["message"]))
			$this->message = $arguments["message"];
		else if(isset($arguments[0]) && is_string($arguments[0]))
			$this->message = $arguments[0];

		// Get the http code from the arguments.
		if(isset($arguments["code"]) && is_integer($arguments["code"]))
			$this->code = $arguments["code"];

		http_response_code($this->code);

		$leum->SetTitle("Error " . $this->code);
		
	}

	public function Content()
	{?>
<div class="main">
	<div class="header">
		<h1><?=$this->code;?> - Uh-Oh!</h1>
		<h2>Yep, you read it correctly, it's a <?=$this->code;?> alright.</h2>
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
