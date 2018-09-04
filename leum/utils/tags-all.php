<?php
if(!defined('SYS_ROOT'))
	define('SYS_ROOT', realpath(__DIR__ . "/../.."));
require_once SYS_ROOT . "/leum/core/leum-core.php";

header("Content-Type: text/plain");

$defaultTags = array();

if(isset($_GET['defaults']))
{
  foreach(explode(',',$_GET['defaults']) as $item)
  {
    $defaultTags[] = Tag::CreateSlug($item);  
  }
}
if(isset($_GET['dir']))
	$dir = $_GET['dir'];

if(!isset($dir))
	throw new Exception("dir must be provided");
	

$dbc = DBConnect();

$batch = 0;
$batchCount = 1;
$batchSize = 100;
$totalBatches = null;

$allKnownTags = Tag::GetAllSlugs($dbc);

DoBatch($dbc, $dir, $batch, $batchSize);
flush();

for ($batch = 1; $batch < $totalBatches; $batch++, $batchCount++)
{
	DoBatch($dbc, $dir, $batch, $batchSize);
	flush();
}

function DoBatch($dbc, $dir, $batch, $size)
{
	global $totalBatches, $overwrite;

	//$items = Media::GetAll($dbc, $batch, $size);
	$items = GetAllInDir($dbc, $dir, $batch, $size);
	
	if($totalBatches == null)
		$totalBatches = ceil(LeumCore::GetTotalItems($dbc) / $size);

	foreach ($items as $item)
	{
		DoSingle($dbc, $item);
	}

	echo "Tag Batch " . ($batch + 1) . " of $totalBatches\n";
}

function GetAllInDir($dbc, $dir, $page, $pageSize)
{
	$offset = $pageSize * $page;
	// Get ALL items
	$sql = "SELECT sql_calc_found_rows * from media where path LIKE CONCAT(?, '%') order by date desc limit ? offset ?";

	$statement = $dbc->prepare($sql);
	$statement->execute([$dir,$pageSize, $offset]);

	return $statement->fetchAll(PDO::FETCH_CLASS, 'Media');
}

function DoSingle($dbc, $media)
{
	global $allKnownTags;

	$tags = $media->GetTags($dbc, true);

	$dirname = str_replace('\\', '/', dirname($media->path));

	$fileTags = explode('/', $dirname);

	foreach ($fileTags as $value)
	{
		$slug = Tag::CreateSlug($value);

		if(empty($slug))
			continue;

		if(!in_array($slug, $tags))
		{
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
