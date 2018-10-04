<?php
/**
 * mediaQuery
 * Builds queries to get and filter media.
 */
class MediaQuery
{
	private $dbc;

	protected $sqlParts = array();
	protected $argumentParts = array();

	protected $allowedFields = [
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
	protected $allowedTables = [
		'media',
		'tags',
		'tag_map',
	];

	private $fields = array();

	const FIELDS = 0;
	const JOIN_TAGS = 3;
	const WHERE = 5;
	const GROUP = 6;
	const ORDER = 7;
	const PAGINATE = 8;

	public function __construct($dbc)
	{
		$this->dbc = $dbc;

		$this->Fields(['*']);
	}

	public function Fields($fields)
	{
		if(!is_array($fields))
			throw new Exception("Argument not an array");

		// Make sure all the fields are allowed.
		$this->CheckFields($fields);
		$this->fields = $fields;
		// Add all the fields to the sql.
		$this->sqlParts[self::FIELDS] = "SELECT sql_calc_found_rows %(fields) from media";
	}

	public function Tags($tags)
	{
		// Join in the tags and tag_map tables.
		$this->sqlParts[self::JOIN_TAGS] =
		"left join tag_map on media.id = tag_map.media left join tags on tag_map.tag = tags.id";

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
	public function Order($field = "date", $direction = "desc")
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
	public function GetSlugs()
	{
		$sql = "(SELECT DISTINCT GROUP_CONCAT(t.slug)
			FROM tags t
			JOIN tag_map tm on t.id = tm.tag
			where tm.media = media.id) as slugs";
		array_push($this->fields, $sql);
	}
	public function Type($type)
	{
		$sql = "type = ?";
		if($type === NULL)
			$sql = "type is NULL";

		// WHERE has already been done.
		if(isset($this->sqlParts[self::WHERE]))
		{
			$this->sqlParts[self::WHERE] .= " and $sql";

			// Add the type only if it's not null.
			if($type !== NULL)
				array_push($this->argumentParts[self::WHERE], $type);
		}
		// WHERE has not been done yet.
		else
		{
			$this->sqlParts[self::WHERE] = "where $sql";
			
			// Add the type only if it's not null.
			if($type !== NULL)
				$this->argumentParts[self::WHERE] = [$type];
		}
	}
	public function Pages($page = 0, $pageSize = PAGE_SIZE)
	{
		// Get the number of items to offset by.
		$pageOffset = $page * $pageSize;

		// Limit the items with the offset.
		$this->sqlParts[self::PAGINATE] = "limit ? offset ?";
		$this->argumentParts[self::PAGINATE] = [$pageSize, $pageOffset];
	}
	public function Execute($singular = false)
	{
		// Limit the query to one item by overwriting the PAGINATE query and arguments.
		if($singular == true)
		{
			// Chuck a warning if paginate has been overwritten.
			if(isset($this->sqlParts[self::PAGINATE]))
				LeumCore::WriteWarning("mediaQuery: Pagination has been overwritten due to being in single mode");

			$this->sqlParts[self::PAGINATE] = "limit 1";
			$this->argumentParts[self::PAGINATE] = [];
		}
		
		$args = [];

		try
		{
			// Sort the argument array by the indices.
			ksort($this->argumentParts, SORT_NUMERIC);

			// convert argumentParts from an array of arrays to one big one.
			foreach ($this->argumentParts as $chunks)
			{
				foreach ($chunks as $arg)
					array_push($args, $arg);
			}

			// Replace '%(fields)' with the fields.
			if(isset($this->sqlParts[self::FIELDS]))
			{
				$fieldText = implode(', ', $this->fields);
				$this->sqlParts[self::FIELDS] = str_replace('%(fields)', $fieldText, $this->sqlParts[self::FIELDS]);
			}

			// Sort the sql argument array by the indices.
			ksort($this->sqlParts, SORT_NUMERIC);
			// Join the SQL.
			$sql = implode(PHP_EOL, $this->sqlParts) . ';';

			$statement = $this->dbc->Prepare($sql);

			$statement->Execute($args);

			return $statement->fetchAll(PDO::FETCH_CLASS, 'Media');
		}
		catch (exception $e)
		{
			// Vardump the arguments so we can put it in a message.
			ob_start();
			var_dump($args);
			$argsMsg = ob_get_clean();

			// output the SQL and parameters sent to the database.
			Message::Create("debug", $sql, "exception");
			Message::Create("debug", $argsMsg, "exception");

			// Throw the exception so the rest of leum can handle the exception.
			throw $e;
		}
	}
	
	/**
	 * Verify that the provided field/column exists in the list of allowed fields.
	 * @param string $fields The field/column that we are checking.
	 *
	 * This will throw an exception if the field does not exist.
	 */
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