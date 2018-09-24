<?php
/**
 * mediaQuery
 * Builds queries to get and filter media.
 */
class MediaQuery
{
	private $dbc;
	private $sql;
	private $fields;

	private $sqlParts = array();
	private $argumentParts = array();

	public $allowedFields = [
		// Allowed fields from media.
		'*',				'media.*',
		'id',				'media.id',
		'parent',			'media.parent',
		'type',				'media.type',
		'title',			'media.title',
		'description',		'media.description',
		'file',				'media.file',
		'date',				'media.date',

		// Allowed fields from tags.
		'tags.*',			'tags.id',
		'slug',				'tags.slug',

		// Allowed fields from tag_map.
		'tag_map.*',		'tag_map.media',
		'tag_map.tag',
	];
	public $allowedTables = [
		'media',
		'tags',
		'tag_map',
	];

	const FIELDS = 0;
	const JOIN_TAGS = 3;
	const WHERE = 5;
	const GROUP = 6;
	const ORDER = 7;
	const PAGINATE = 8;

	function __construct($dbc)
	{
		$this->dbc = $dbc;

		$this->sqlParts[self::FIELDS] = "SELECT * from media";
	}

	function Fields($fields)
	{
		// Make sure all the fields are allowed.
		$this->CheckFields($fields);

		$fields = implode(', ', $fields);
		// Add all the fields to the sql.
		$this->sqlParts[self::FIELDS] = "SELECT sql_calc_found_rows $fields from media";
	}

	function Tags($tags)
	{
		// Join in the tags and tag_map tables.
		$this->sqlParts[self::JOIN_TAGS] =
		"left join tag_map on media.id = tag_map.media
left join tags on tag_map.tag = tags.id";

		// Only select the tag slugs we want.
		$placeholder = LeumCore::PDOPlaceholder($tags);

		// If WHERE was already set and the slugs where.
		if(isset($this->sqlParts[self::WHERE]))
		{
			$this->sqlParts[self::WHERE] .= " and tags.slug in ( $placeholder )";
			$this->argumentParts[self::WHERE] = 
				array_merge($this->argumentParts[self::WHERE],$tags);
		}
		// We where first on where, let's create it.
		else
		{
			$this->sqlParts[self::WHERE] = "where tags.slug in ( $placeholder )";
			$this->argumentParts[self::WHERE] = $tags;
		}

		// Only one of each media id.
		$this->sqlParts[self::GROUP] = "group by id";
	}
	function Order($field = "date", $direction = "desc")
	{
		// Make sure the field is allowed.
		$this->CheckFields($field);

		// Make sure the direction is allowed.
		$direction = strtolower($direction);
		if($direction !== "desc" && $direction !== "asc")
			throw new Exception("$direction is not allowed. Only 'desc' and 'asc' are allowed.");

		// Add the sql and parameters to the query.
		$this->sqlParts[self::ORDER] = "order by $field $direction";
	}
	function Type($type)
	{
		$sql = "type = ?";
		if($type === NULL)
		{
			$sql = "type is NULL";
		}
		if(isset($this->sqlParts[self::WHERE]))
		{
			$this->sqlParts[self::WHERE] .= " and $sql";
			
			if($type !== NULL)
				array_push($this->argumentParts[self::WHERE], $type);
		}
		else
		{
			$this->sqlParts[self::WHERE] = "where $sql";
			
			if($type !== NULL)
				$this->argumentParts[self::WHERE] = [$type];
		}
	}
	function Pages($page = 0, $pageSize = PAGE_SIZE)
	{
		// Get the number of items to offset by.
		$pageOffset = $page * $pageSize;

		// Limit the items with the offset.
		$this->sqlParts[self::PAGINATE] = "limit ? offset ?";
		$this->argumentParts[self::PAGINATE] = [$pageSize, $pageOffset];
	}
	function Execute()
	{
		try
		{
			$args = [];

			foreach ($this->argumentParts as $chunks)
			{
				foreach ($chunks as $arg)
				{
					array_push($args, $arg);
				}
			}
			ob_start();
			var_dump($args);
			$argsMsg = ob_get_clean();

			ksort($this->sqlParts, SORT_NUMERIC);
			$sql = implode(PHP_EOL, $this->sqlParts) . ';';


			$statement = $this->dbc->Prepare($sql);

			$statement->Execute($args);

			return $statement->fetchAll(PDO::FETCH_CLASS, 'Media');
		}
		catch (exception $e)
		{
			Message::Create("debug", $sql);		
			Message::Create("debug", $argsMsg);
			throw $e;
		}
	}
	function CheckFields($fields)
	{
		if(is_array($fields))
		{
			$badFields = array_diff($fields, $this->allowedFields);
			if(count($badFields) > 0)
				throw new Exception("The '" . implode(', ', $badFields) . "' fields/columns are not allowed.");
		}
		else if(!in_array($fields, $this->allowedFields))
		{
			throw new exception("The '$fields' fields/columns are not allowed.");
		}

		return true;
	}
}

?>