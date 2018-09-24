<?php
// TODO: Depreciate this file. It's not needed and some functions are 'core'
function DBConnect()
{
	// TODO: Put this in LeumCore __construct as leumCore is loaded by both API and front.
	global $prefrences;
	$opt = [
		PDO::ATTR_ERRMODE				=> PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE	=> PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES		=> false,
	];
	
	$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME .";charset=utf8", DB_USER, DB_PASS, $opt);
	return $pdo;
}

// deprecate
function Asset($asset)
{
	echo GetAsset($asset);
}
// move to front.
function GetAsset($asset)
{
	return ROOT.$asset;
}
// move to front, deprecate or put in users.
function ProfilePicture($colour = "#4eb4f5")
{
	if(isset(Leum::Instance()->user))
	{
		$profileUrl = GetAsset("/resources/graphics/default-profile-1.png");
		echo "<img class=\"profile\" src=\"$profileUrl\" style=\"background-color: $colour\">";
	}
	else
		echo "<i class=\"fa fa-user-circle\"></i>";
}

// deprecate
function TheTitle()
{
	echo Leum::Instance()->title;
}

// deprecate
function TheContent()
{
	if(Log::$highest != false)
		Message::Create("info", "Wrote one ore more messages to the logs. Highest message was <i>" . Log::$logLevels[Log::$highest] . "</i>");
	Message::ShowMessages("default");
	Leum::Instance()->Output();
}

// deprecate
function TheHead()
{
	Leum::Instance()->Head();
}

// deprecate
function CreateSlugString($tags)
{
	if(count($tags) == 0)
		return;

	if($tags[0] instanceof Tag)
		$tags = array_map(create_function('$tg', 'return $tg->slug;'), $tags);

	return implode('+', $tags);
}
// deprecate
function ParseSlugString($string)
{
	$string = preg_replace("[^A-Za-z0-9-]", "", $string);
	$slugs = explode(',', $string);
	$slugs = array_filter($slugs);
	return $slugs;
}

// why is this not in thumbnails? It's clearly for thumbnails.
function ImageCreateFromImage($file)
{
	if(!file_exists($file))
		throw new InvalidArgumentException("File '$file' not found.");
	switch (strtolower(pathinfo($file, PATHINFO_EXTENSION)))
	{
		case 'jpeg':
		case 'jpg':
			return imagecreatefromjpeg($file);

		case 'png':
			return imagecreatefrompng($file);

		case 'gif':
			return imagecreatefromgif($file);
	}

	throw new InvalidArgumentException("File is not a png, jpg or gif");
	
		
}

?>
