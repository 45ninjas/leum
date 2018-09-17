<?php
ob_start();
define('SYS_ROOT', __DIR__);
include_once 'leum/leum.php';
$leumContent = trim(ob_get_clean());

if(!empty($leumContent))
	Message::Create("msg-debug",$leumContent);
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
<html class="<?=Leum::Instance()->pageClass?>">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, inital-scale=1.0">
	<title><?php TheTitle(); ?></title>
	<link rel="stylesheet" type="text/css" href="<?php Asset("/resources/css/pure-min.css"); ?>">
	<link rel="stylesheet" type="text/css" href="<?php Asset("/resources/css/grids-responsive-min.css"); ?>">
	<link rel="stylesheet" type="text/css" href="<?php Asset("/resources/css/font-awesome.min.css"); ?>">
	<script type="text/javascript" src="<?php Asset("/resources/js/jquery-3.2.1.min.js"); ?>"></script>
	<meta property="site-root" content="<?php echo ROOT; ?>">
	<meta property="api-url" content="<?php echo API_URL; ?>">
	<?php TheHead(); ?>
	<link rel="stylesheet" type="text/css" href="<?php Asset("/resources/css/leum.css"); ?>">
</head>
<body class="static-title <?=Leum::Instance()->pageClass?>">
	<?php include "leum/page-parts/leum-menu.php" ?>
	<?php TheContent(); ?>
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
	<script type="text/javascript" src="<?php Asset("/resources/js/modal.js"); ?>"></script>
	<script type="text/javascript" src="<?php Asset("/resources/js/ui.js"); ?>"></script>

</body>
</html>