<?php
if(!defined('SYS_ROOT'))
	define('SYS_ROOT', realpath(__DIR__ . "/.."));
require_once SYS_ROOT . "/utils/thumbnails-all.php";
require_once SYS_ROOT . "/core/leum-core.php";

header("Content-Type: text/plain");

$dbc = DBConnect();

$overwrite = isset($_GET['overwrite']);

$batch = 0;
$batchCount = 1;
$batchSize = 100;
$totalBatches = null;

DoBatch($dbc, $batch, $batchSize);
flush();
die();

for ($batch = 1; $batch < $totalBatches; $batch++, $batchCount++)
{
	DoBatch($dbc, $batch, $batchSize);
	flush();
}

function DoBatch($dbc, $batch, $size)
{
	global $totalBatches, $overwrite;

	$items = Media::GetAll($dbc, $batch, $size);
	
	if($totalBatches == null)
		$totalBatches = ceil(LeumCore::GetTotalItems($dbc) / $size);

	echo "Batch " . ($batch + 1) . " of $totalBatches\n";

	$result = Thumbnails::MakeForMultiple($dbc, $items, $overwrite);
	echo "\tFailed: " . $result['failed'] . "\n";
	echo "\tSkipped: " . $result['skipped'] . "\n";
	echo "\tSuccess: " . $result['success'] . "\n";
}
echo "Done\n";
?>