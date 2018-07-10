<?php

function DBConnect()
{
	global $prefrences;
	$opt = [
		PDO::ATTR_ERRMODE				=> PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE	=> PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES		=> false,
	];
	
	$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME .";charset=utf8", DB_USER, DB_PASS, $opt);
	return $pdo;
}

function Asset($asset)
{
	echo GetAsset($asset);
}
function GetAsset($asset)
{
	return ROOT.$asset;
}
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

function TheTitle()
{
	echo Leum::Instance()->title;
}
function TheContent()
{
	Message::ShowMessages("default");
	Leum::Instance()->Output();
}
function TheHead()
{
	Leum::Instance()->Head();
}

function CreateSlugString($tags)
{
	if(count($tags) == 0)
		return;

	if($tags[0] instanceof Tag)
		$tags = array_map(create_function('$tg', 'return $tg->slug;'), $tags);

	return implode('+', $tags);
}
function ParseSlugString($string)
{
	$string = preg_replace("[^A-Za-z0-9-]", "", $string);
	$slugs = explode(',', $string);
	$slugs = array_filter($slugs);
	return $slugs;
}

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
