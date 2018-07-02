<?php 
/**
 * Dispatcher.
 * This converts url's into pages.
 */
class Dispatcher
{
	// This mess is very closly related to the code outlined in this guide
	// http://nikic.github.io/2014/02/18/gast-request-routing-using-regular-expressions.html
	// Each group of the regex command will be added to an array whether or not
	// it actually matched. Therefore we can map each regex group to an array item.
	// However some routes require more than one regex group. A route is
	// duplicated by however many arguments it contains. Routes with no arguments
	// also cause problems. So routes with no arguments get given an empty route.
	private $regex;
	private $pages;
	public function __construct($routes)
	{
		// Create an array to store the route files.
		$this->pages = array();

		// Create one massive regular expression from the ones listed in the
		// preferences file.
		$this->regex = '~^(?:';
		foreach ($routes as $route)
		{
			$this->AddRoute($route);	
		}
		$this->regex .=')$~x';
	}
	private function AddRoute($route)
	{
		$page = $route[0];
		$regex = $route[1];
		$total = $route[2];
		
		// If there are no parameters then add a dummy (blank) group.
		if($total == 0)
		{
			$total = 1;
			$regex.='()';
		}

		// OR the regular expression to the existing expressions.
		$this->regex .= ' | '.$regex;
		
		// Add one or more route(s) based on how many groups each expression has.
		for ($i=0; $i < $total; $i++)
		{ 
			$this->pages[] = $page;
		}
	}

	public function Dispatch($uri)
	{
		// Run the regular expression against the URI and get the matches.
		if(preg_match($this->regex, $uri, $matches))
		{	
			// Remove the first match because it's the whole string that matched.
			array_shift($matches);

			// Create an array to store each argument of the route.
			$args = [];
			// Add the non-empty matches to the list of arguments.
			for ($i=0; $i < count($matches); $i++)
			{
				if(!empty($matches[$i]))
					$args[] = $matches[$i];
			}

			array_unshift($args, $this->pages[count($matches) - 1]);
			return $args;
			
		}
		return false;
	}
	public function GetPage($request)
	{
		if($request == "")
			return ["home.php"];

		$args = $this->Dispatch($request);
		return $args;
	}
}