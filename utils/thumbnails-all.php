<?php
if(!defined('SYS_ROOT'))
	define('SYS_ROOT', realpath(__DIR__ . "/.."));
require_once SYS_ROOT . "/utils/thumbnails-all.php";
require_once SYS_ROOT . "/core/leum-core.php";

$dbc = DBConnect();

$overwrite = isset($_GET['overwrite']);

$batch = 0;
$batchCount = 1;
$batchSize = 100;

DoBatch($batch, $batchSize);

$totalBatches = ceil(LeumCore::GetTotalItems($dbc) / $batchSize);
echo "Batch $batchCount of $totalBatches\n";
flush();
Thumbnails::MakeForMultiple($dbc, $items);

for ($batch = 1; $batch < $totalBatches; $batch++, $batchCount++)
{
	$items = Media::GetAll($dbc, $batch, $batchSize);
	echo "Batch $batchCount of $totalBatches\n";
	flush();
	$result = Thumbnails::MakeForMultiple($dbc, $items, $overwrite);
}

function DoBatch($batch, $size)
{
	global $totalBatches;
	$result = Media::GetAll($dbc, $batch, $size);
	list($result);
	echo "Batch \t$batch of \t$totalBatches.\n";
	echo "\tFailed: $failed\n";
	echo "\tSkipped: $skipped\n";
	echo "\tSuccess: $success\n";
}
echo "Done\n";
?>