<?php 
// Leum setup. Run this the first time you install leum.

include_once "functions.php";

$db = DBConnect();
echo "Connected to database successfully\n";

// === Media Table ===
$sql = "CREATE TABLE media
(
	media_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(256) NOT NULL,
	source TEXT NOT NULL,
	path VARCHAR(256) NOT NULL,
	date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$db->exec($sql);
echo "Created the media table.\n";


$sql = "CREATE TABLE tags
(
	tag_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	slug VARCHAR(32) NOT NULL UNIQUE KEY,
	title VARCHAR(256) NOT NULL
)";

$db->exec($sql);
echo "Created the tags table.\n";

$sql = "CREATE TABLE map
(
	map_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	tag_id INT UNSIGNED NOT NULL,
	media_id INT UNSIGNED NOT NULL,
	FOREIGN KEY (media_id) REFERENCES media(media_id)
		ON DELETE CASCADE,
	FOREIGN KEY (tag_id) REFERENCES tags(tag_id)
		ON DELETE CASCADE
)";

$db->exec($sql);
echo "Created the map table.\n";

echo "\nDone\n";

?>