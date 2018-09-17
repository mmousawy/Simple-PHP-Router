<?php

return function(&$router) {
	$currentPath = $router->currentPath;

	return [
		'title' => 'Simple PHP Router',
		'content' => '<h1>This page does not exist!</h1><p>The provided URL did not resolve to a valid route and returned the 404 page.</p><pre>Path: ' . $currentPath . '</pre>'
	];
};
