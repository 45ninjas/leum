<!DOCTYPE html>
<html>
<head>
	<title>View Testing Tags</title>
	<style type="text/css">
		html, body
		{
			background-color: #07050f;
			color: #debc79;
			/*font-family: monospace;*/
			font-size: 100%;
		}
		hr
		{
			border: dashed 1px #debc79;
		}
		h1, h2, h3
		{
			margin: 0;
		}
		table
		{
			/*border-collapse: collapse;*/
			border: 1px solid;
			margin: auto;
			width: 100%;
			max-width: 70em;
		}
		thead
		{
			background-color: #debc79;
			color: #07050f;
		}
		tbody tr
		{
			border-top: 1px solid #312822;
		}
		tbody tr:nth-child(odd)
		{
			background-color: #debc791a;
		}
		td, th
		{
			padding: 2px 10px;
		}
	</style>
</head>
<body>
	<h2>View Testing</h2>
	<h3>Active view: Tags</h3>
	<hr>
<?php

define('SYS_ROOT', __DIR__ . "../../");
require_once '../preferences.php';
require_once SYS_ROOT . "/core/leum-core.php"; 
require_once SYS_ROOT . "/views/tags/tags-edit.php";
require_once SYS_ROOT . "/views/media/media-edit.php";
require_once SYS_ROOT . "functions.php";


$dbc = DBConnect();

$tags = Tag::GetAll($dbc);
$media = Media::GetAll($dbc);

$tagView = new TagView();
$tagView->DoTable($tags);

$mediaView = new MediaView();
$mediaView->DoTable($media);

?></body>
</html>