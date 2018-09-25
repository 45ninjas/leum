MediaQuery
===========

The media query class is used to query media in a slightly less insane way.

The query is broken into little bite modular chunks. Upon calling each method the a part of the query is added to the ``sqlParts`` array. If any PDO parameters are required they too get added as an array to the ``argumentsParts`` array.

Once the Query is built ``Execute()`` is called. It will return whatever the query returns.

Class Overview
--------------

.. code-block:: php

	MediaQuery
	{
		/* Properties */
		protected array $sqlParts;
		protected array $argumentParts;
		protected array $allowedFields;
		protected array $allowedTables;

		/* Constants */
		const int FIELDS = 0;
		const int JOIN_TAGS = 3;
		const int WHERE = 5;
		const int GROUP = 6;
		const int ORDER = 7;
		const int PAGINATE = 8;

		/* Methods */
		public __construct (PDO $dbc)
		public void Fields (array $fields)
		public void Tags (array $tags)
		public void Order (string $field = "date", string $direction = "desc")
		public void Type (mixed $type)
		public void Pages (int $page = 0, int $pageSize = PAGE_SIZE)
		public mixed Execute ()
	}

Methods
-------

Fields(array $fields)
	The fields method sets the columns to select from the database. If any item in the ``$fields`` array is not in ``$allowedFields`` an exception is thrown.

Tags(array $tags)
	Join the *tags* and *tag_map* tables and only return media with any of the provided tags. ``$tags`` is an array of tag slugs.

Order(string $field, string $direction)
	Order the query using the specified field and direction. If the field is not in the ``$allowedFields`` array an exception is thrown. If direction is anything other than *desc* or *asc* an exception is thrown.

Type(mixed $type)
	When type is a string the query will only get media with the same value in the media.type column. However when type is NULL the query will return media with no value set.

Pages(int $page, int $pageSize)
	Pagination. It's pretty self explanatory.


Properties
----------
sqlParts
	An array that holds parts of an SQL query. The Constants are used as indexes for this array.
argumentParts
	An array that holds arrays of parameters for the query. The Constant are also used as indexes.
allowedFields
	This array contains all allowed fields for this query builder. PDO is unable to pass columns/fields and must be done in SQL. This array is used to check for invalid data like SQL injection attacks.
allowedTables
	This array contains all allowed tables for this query builder. For the same reasons as above.


Constants
---------

The constants are used to help organize the indices of both the sqlParts and argumentParts.

FIELDS
	Fields are done first. It's used to create ``SELECT media.* FROM media``.
JOIN_TAGS
	Used next to create inner joins for both the ``tag_map`` and ``tags`` table.
WHERE
	Creates the where parts. This is used in multiple places so both the ``Type`` and ``Tags`` components of the query must be able to append it's self to the existing query.
GROUP
	Groups the data on the ``media.id`` and is internally used on ``JOIN_TAGS``
ORDER
	Done to order the data.
PAGINATE
	Finally, the data is paginated into little cute blocks of 250 (by default.)
