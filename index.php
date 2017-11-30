<?php
include_once 'leum.php';
// Leum.
// The simple local media tagging system.
// Designed to store your webm, gif and other content.
//
// ==== Warning ====
// This is NOT designed to be used as a production site. Security
// is non-existent at this time and probably for a while. This is 
// an Intranet-site Not A Web-Site.

// Do the wizardry here.

?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, inital-scale=1.0">
	<title><?php TheTitle(); ?></title>
	<link rel="stylesheet" type="text/css" href="<?php Asset("/resources/css/pure-min.css"); ?>">
	<link rel="stylesheet" type="text/css" href="<?php Asset("/resources/css/side-menu.css"); ?>">
	<link rel="stylesheet" type="text/css" href="<?php Asset("/resources/css/leum-modal.css"); ?>">
	<link rel="stylesheet" type="text/css" href="<?php Asset("/resources/css/grids-responsive-min.css"); ?>">
	<link rel="stylesheet" type="text/css" href="<?php Asset("/resources/css/leum.css"); ?>">
	<link rel="stylesheet" type="text/css" href="<?php Asset("/resources/css/font-awesome.min.css"); ?>">
	<script type="text/javascript" src="<?php Asset("/resources/js/jquery-3.2.1.min.js"); ?>"></script>
	<?php TheHead(); ?>
</head>
<body>
	<div id="layout">
		<?php include "page-parts/side-bar.php"; ?>
		<?php TheContent(); ?>
	</div>


	<!-- Modal Stuff -->
	<div id="modal-background" hidden></div>

	<template id="modal-template">
		<div class="modal">
			<h2 class="modal-title"></h2>
			<div class="content">
			</div>
			<div class="modal-footer">
			</div>
		</div>
	</template>
	<script type="text/javascript" src="<?php Asset("/resources/js/ui.js"); ?>"></script>
	<script type="text/javascript" src="<?php Asset("/resources/js/modal.js"); ?>"></script>

</body>
</html>