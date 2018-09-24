<?php
/**
 * Open Movie Database provider plug-in.
 */
class OMDB_source extends Plugin
{
	static $hasKey = false;
	public function __construct()
	{
		if(!defined('OMDB_API_KEY'))
			return;

		self::$hasKey = true;
	}
	public function SavePoster($imdb_id, $file)
	{
		if(!self::$hasKey)
			return false;

		file_put_contents($file, "http://img.omdbapi.com/?$query");
	}
	public static function MakeRequest($data, $apiKey = self::API_KEY)
	{
		if(!self::$hasKey)
			return false;

		$data['apikey'] = $apiKey;

		// Build a http query (get string).
		$query = http_build_query($data);

		$raw = file_get_contents("http://www.omdbapi.com/?$query");

		if($raw === false)
		{
			Log::Write("There was an error while making a request to OMDBAPI", Log::WARNING);
			return false;
		}

		$result = json_decode($raw, true);

		return $result;
	}
	public static function TestOMDB()
	{
		$request = self::MakeRequest(['i'=>'tt0149460'], self::API_KEY, false);
		if(isset($request['Title']))
		{
			return $request['Title'] === "Futurama";
		}
		return false;
	}
}

?>