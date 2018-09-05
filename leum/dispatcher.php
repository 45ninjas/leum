<?php 
/**
 * Dispatcher.
 * This converts url's into pages.
 */
class Dispatcher
{
	const BAD_ROUTE = 0;
	const FOUND = 1;
	const NOT_FOUND = 2;

	private static $routes = array();
	private static $defaultArgs = array
	(
		// An index is more than characters of 0 to 9
		'%index%'	=> '([0-9]+)',
		// Slugs can only have - a to z and 0 to 9.
		'%slug%'	=> '([-a-z0-9]+)',
		// '%extend%'	=> '(*)'
	);
	static function AddRoute($route, $target)
	{
		// Replace all the keys of the defaultArgs array with the value of defaultArgs.
		// Save how many things where changed.
		$route = str_replace
		(
			array_keys(self::$defaultArgs),
			array_values(self::$defaultArgs),
			$route,
			$count
		);
		self::$routes[] = [$route, $count, $target];
	}
	private static function PrepareResolve(&$groups)
	{
		// Create the regex string and setup the group map.
		$groups = array();
		$regexStrings = array();

		foreach (self::$routes as $key => $route)
		{
			// Get the components from the routes.
			$routeString = $route[0];
			$count = $route[1];
			$target = $route[2];

			// No arguments.
			if($count == 0)
			{
				$count = 1;
				$regexStrings[] = $routeString . '()';
				$groups[] = $key;
			}
			// More than one argument.
			else
			{
				$regexStrings[] = $routeString;
				for ($i=0; $i < $count; $i++)
					$groups[] = $key;
			}
		}
		
		return '#^(?:' . implode('|', $regexStrings) . ')$#';
	}
	static function ResolveRoute($request)
	{
		// Create the regex strung and groupMap.
		$groupMap = null;
		$regex = self::PrepareResolve($groupMap);

		// Set result to bad route if anything goes wrong.
		$result = ['state' => self::BAD_ROUTE ];

		// Attempt to match the request with the regex string.
		if(preg_match($regex, $request, $matches))
		{
			// The first result of matches is the request, remove it.
			array_shift($matches);

			// Put the not empty matches into the args array.
			$args = [];
			for ($i=0; $i < count($matches); $i++)
			{ 
				if(!empty($matches[$i]))
					$args[] = $matches[$i];
			}

			// Save the result.
			$result['params'] = $args;

			// Make sure that we actually have something.
			if(isset($groupMap[count($matches) -1]))
			{
				$result['target'] = self::$routes[$groupMap[count($matches) - 1]][2];
				$result['state'] = self::FOUND;
			}
		}

		// Looks like no matches where found.
		else
			$result['state'] = self::NOT_FOUND;

		return $result;
	}
}