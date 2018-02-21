<?php
if(!defined('SYS_ROOT'))
	define('SYS_ROOT', realpath(__DIR__ . "/.."));
require_once SYS_ROOT . "/core/leum-core.php";

header("Content-Type: text/plain");

$dbc = DBConnect();

$batch = 0;
$batchCount = 1;
$batchSize = 100;
$totalBatches = null;

$allKnownTags = Tag::GetAll($dbc);

DoBatch($dbc, $batch, $batchSize);
flush();

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

	foreach ($items as $item)
	{
		DoSingle($dbc, $item);
	}

	echo "Tag Batch " . ($batch + 1) . " of $totalBatches\n";
}

function DoSingle($dbc, $media)
{
	global $allKnownTags;
	
	$tags = $media->GetTags($dbc, true);
	var_dump($tags);

	$dirname = str_replace('\\', '/', dirname($media->path));

	$fileTags = explode('/', $dirname);
	var_dump($fileTags);

	foreach ($fileTags as $value)
	{
		$slug = Tag::CreateSlug($value);

		if(!in_array($slug, $tags))
		{
			echo "$slug\n";
			array_push($tags, $slug);

			if(!in_array($slug, $allKnownTags))
			{
				AddTag($dbc, $slug);
			}
		}
	}

	Mapping::SetMappedTags($dbc, $media, $tags);
}
function AddTag($dbc, $slug)
{
	global $allKnownTags;

	if (Tag::InsertSingle($dbc, $slug) > 0)
	{
		array_push($allKnownTags, $slug);
		return;
	}
	else
		throw new Exception("Failed to create tag");

}
echo "Done\n";
?>