<?php

namespace Murtada;

class Route {
	public $route;
	public $params;
	public $path;

	public function __construct($route, $params, $path)
	{
		$this->route = $route;
		$this->params = $params;
		$this->path = $path;
	}
}
