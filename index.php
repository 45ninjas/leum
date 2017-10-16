<?php
include_once 'functions.php';
// Leum.
// The simple local media tagging system.
// Designed to store your webm, gif and other content.
//
// ==== Warning ====
// This is NOT designed to be used as a production site. Security
// is non-existent at this time and probably for a while. This is 
// an Intranet-site Not A Web-Site.

$data = array();
$data['title'] = "Leum Dev";
$data['body'] = '';
$data['head'] = '';
// Do the wizardry here.

?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $data['title']; ?></title>
	<link rel="stylesheet" type="text/css" href="<?php asset("css/pure-min.css"); ?>">
	<link rel="stylesheet" type="text/css" href="<?php asset("css/side-menu.css"); ?>">
	<link rel="stylesheet" type="text/css" href="<?php asset("css/leum-modal.css"); ?>">
</head>
<body>
	<div id="layout">
		<?php include "page-parts/side-bar.php"; ?>
		<div id="main">
			<h1>Hello World!</h1>
		</div>
	</div>
	<div id="modal-background"></div>
	<div class="modal">
		<div class="content">
			<h2>Why Seals Are Dying From Sore Throats</h2>
			<p>Wittle baby seals are getting sore throats and are getting deadded from it. In this modal I will discuss why they are getting sore throats. Before we begin, this is a sad seal, she is only eight weeks old and got as sore throat and died and it was sad. Some <strong>Butter-Menthol&trade;</strong> would have made her survive.</p>
			<img class="pure-img" src="https://dingo.care2.com/pictures/petition_images/petition/334/399892-1487236401-wide.jpg">
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
			tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
			quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
			consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
			cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
			proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			<p>And that's why I believe Donald Trump is the primary reason why seals will die of sore throats.</p>
		</div>
		<div class="modal-footer">
			<button class="pure-button">Retry</button>
			<button class="pure-button pure-button-primary">Smiles</button>
		</div>
	</div>
	<script type="text/javascript" src="<?php asset("js/ui.js"); ?>"></script>
</body>
</html>