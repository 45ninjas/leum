<?php
if(!defined('SYS_ROOT'))
	define('SYS_ROOT', realpath(__DIR__ . "/.."));
require_once SYS_ROOT . "/utils/thumbnails-all.php";
require_once SYS_ROOT . "/core/leum-core.php";

$dbc = DBConnect();

$batch = 0;
$batchCount = 1;
$batchSize = 100;

$items = Media::GetAll($dbc, $batch, $batchSize);
$totalBatches = LeumCore::GetTotalItems($dbc) / $batchSize;
echo "Batch $batchCount of $totalBatches\n";
flush();
Thumbnails::MakeForMultiple($dbc, $items);

for ($batch = 1; $batch < $totalBatches; $batch++, $batchCount++)
{ 
	$items = Media::GetAll($dbc, $batch, $batchSize);
	echo "Batch $batchCount of $totalBatches\n";
	flush();
	Thumbnails::MakeForMultiple($dbc, $items);
}
echo "Done";
?>