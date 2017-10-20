<?php

class FrontCrontroller
{
	private $controller;
	private $view;

	public function __construct(Router $router, $routeName, $action = null)
	{
		// Get the route based on a name, e.g 'search' or 'list'.
		$route = $router->GetRoute($routeName);

		// Get the names of each component from the router.
		$modelName = $route->model;
		$controllerName = $route->controller;
		$viewname = $route->view;

		// Instantiate each component.
		$model = new $modelName;
		$this->controller = new $controllerName($model);
		$this->view = new $viewName($routeName, $model); 

		if(!empty($action)) $this->controller->{$action}();
	}

	public function TheTitle()
	{
		echo $this->view->title;
	}

	public function TheContent()
	{
		$this->view->Output();
	}
}

?>
