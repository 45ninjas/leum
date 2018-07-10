<?php
class QueryReader
{ 
	public $wantedTags = array();
	public $unwantedTags = array();
	public $possibleTags = array();
	
	public function __construct($queryString)
	{
		$queryString = Query::Sanitize($queryString);
		foreach (explode(' ', $queryString) as $part)
		{
			switch (substr($part, 0, 1))
			{
				case '~':
					array_push($this->possibleTags, substr($part, 1));
					break;
				case '-':
					array_push($this->unwantedTags, substr($part, 1));
					break;
				default:
					array_push($this->wantedTags, $part);
					break;
			}
		}
		echo "wantedTags\n";
		var_dump($this->wantedTags);
		echo "unwantedTags\n";
		var_dump($this->unwantedTags);
		echo "possibleTags\n";
		var_dump($this->possibleTags);
	}
}
class Query
{
	public static function Sanitize($string)
	{
		strtolower($string);
		$string = preg_replace("[^a-z0-9~-]", "",  $string);
		return $string;
	}
}
?>