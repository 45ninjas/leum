Getting Media
=============

Getting media in leum is easyish.


Media::GetMedia
---------------

.. code-block:: php

	public static mixed Media::GetMedia(PDO $dbc, array $args)

Retunes a list of media items matching the arguments.

Parameters
""""""""""
$dbc
	The 'DataBase Connection object. (PDO)'
$args (array)
	Arguments for the query. See `Arguments`_ for more.
Example
"""""""
.. code-block:: php

	<?php

	// Not done just yet.

	?>

Arguments
----------

type
	Gets media with the specified type.
tags
	Gets media with any of the tags. This MUST be an array or NULL.
exclude-tags
	Media must NOT have any of these tags. Thus MUST be an array or NULL.
page-size
	The size of pages. Default: ``PAGE_SIZE``. Null = no pages.
page
	Page count. Used in conjunction with page-size.
