<?php

return function(&$router) {
	$params = $router->getParams();
	$paramsOutput = var_export($params, true);

	$tabs = $router->createMenu('test', [
		'prefix' => '<li class="tab"><a class="tab__link%active-class%" href="%link%">',
		'suffix' => '</a></li>',
		'params' => [
			'test-02' => [
				'param1' => rand(1000, 9999),
				'param2' => rand(1000, 9999)
			],
			'test-03' => [
				'param1' => rand(1000, 9999),
				'param2' => rand(1000, 9999)
			]
		],
		'activeClass' => 'tab__link--active'
	]);

	$content =
<<<HTML
<h1>Parameters test page</h1>
<ul class="tabs">{$tabs}</ul>
<pre>
{$paramsOutput}
</pre>
HTML;

	return [
		'title' => 'Parameters test page',
		'content' => $content
	];
};
