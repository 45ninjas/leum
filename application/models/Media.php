<?php
class MediaModel
{
	$db;

	function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Gets All media from the database.
	 */
	public function GetAllMedia()
	{
		$sql = "SELECT * FROM media";
		$query = $this->db->prepare($sql);
		$query->execute();

		return $query->fetchAll();
	}

	/**
	 * Adds a new media item to the database
	 * @param string $title  title of media.
	 * @param string $path   relative path to media.
	 * @param string $source source of media (usually a url).
	 * @param array  $tags   array of tag slugs.
	 */
	public function AddMedia($title, $path, $source = null, $tags = null)
	{
		// TODO: Filter the title and check the path.
		$title = strip_tags($title);
		$path = strip_tags($path);

		if(!is_null($source))
			$source = strip_tags($source);

		// TODO: Implement tags here.
		
		$sql = "INSERT INTO media (title, path, source) VALUES (:title, :path, :source)";
		$query = $this->db->prepare($sql);
		$query->execute(array(
			':title' => $title,
			':path' => $path,
			':source' => $source
		));

		return $query->lastInsertId();
	}
	
	/**
	 * Edit an existing media item.
	 * @param int 	 $media_id	media_id of item to edit.
	 * @param string $title		title of media.
	 * @param string $path		relative path to media.
	 * @param string $source	source of media (usually a url).
	 */
	public function EditMedia($media_id, $title, $path, $source)
	{
		// TODO: Filter the title and check the path.
		$title = strip_tags($title);
		$path = strip_tags($path);
		$source = strip_tags($source);

		$sql = "UPDATE media SET title = :title, path = :path, source = :source WHERE media_id = :media_id";
		$query = $this->db->prepare($sql);
		$query->execute(array(
			':title' => $title,
			':path' => $path,
			':source' => $source,
			':media_id' => $media_id
		));
	}

	/**
	 * Remove a media item from the database
	 * @param int $mediaId media_id index.
	 */
	public function DeleteMedia($mediaId)
	{
		$sql = "DELETE FROM media WHERE media_id = :media_id";
		$query = $this->db->prepare($sql);
		$query->execute(array(
			':media_id' => $mediaId
		));
	}
}
?>
