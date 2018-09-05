Dispatcher
==========

The dispatcher is used to route pages to urls.
The dispatcher currently supports index and slug variables.

Constants
---------
Dispatcher\:\:BAD_ROUTE
	Is used when a route was found but another error occurred.
Dispatcher\:\:FOUND
	When a route has been found.
Dispatcher\:\:NOT_FOUND
	Used when a route was not found like 404.

Route Variables
---------------
%index%
	An unlimited length of numerical characters.
	Regex equivalent: ``([0-9]+)``.

	Examples: ``302``, ``2202``, ``129``
%slug%
	Characters ``a to z``, ``0 to 9`` and ``-``.
	Regex equivalent: ``([-a-z0-9]+)``.

	Examples: ``the-grand-tour``, ``mcmtv2``, ``300``, ``iron-man-2``

Dispatcher:AddRoute
-------------------

.. code-block:: php

	static void Dispatcher::AddRoute(string $route, object $target)

Adds a route to the target.

Parameters
^^^^^^^^^^

route
	The route string is a url like string. This string supports the variables listed below.
target
	The target object. Currently leum only supports a path to a page. It's capable of supporting anonymous functions however leum is not.

Example
^^^^^^^
.. code-block:: php

	<?php
		// /leum/testing/example/1498 goes to pages/testing.php.
		// 1498 is passed as an argument for the page.
		Dispatcher::AddRoute('testing/example/%index%', 'pages/testing.php');
		// /leum/fan-clubs/teen-titans-go will take you to clubs.php
		// while passing %slugs% to the page.
		Dispatcher::AddRoute('fan-clubs/%slug%', 'clubs.php');v
	?>





Dispatcher:ResolveRoute
-------------------

.. code-block:: php

	static void Dispatcher::ResolveRoute(string $request)

Attempts to resolve a route for the request.

Parameters
^^^^^^^^^^

request
	The url or path that needs to be resolved.

Returns
^^^^^^^
	Array with up to three elements.

state
	One of three constants above, This is always returned.
params
	Array of arguments. Only set if state is 0 (``Dispatcher::BAD_ROUTE``) or 1 (``Dispatcher::FOUND``)
target
	The target object provided with ``AddRoute``. This is a string by anything is allowed.

Example
^^^^^^^
.. code-block:: php

	<?php
		// Add the only route, fan-clubs/%slug%
		Dispatcher::AddRoute('fan-clubs/%slug%', 'clubs.php');

		Dispatcher::ResolveRoute('fan-clubs/teen-titans-go');
		/* Returns
		[
			"state" => 1,
			"params"=> [ "teen-titans-go" ],
			"target" => "clubs.php"
		]*/

		Dispatcher::ResolveRoute('the-bad/egg');
		/* Returns
		[
			"state" => 2
		]*/
	?>