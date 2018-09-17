<?php

return function(&$router) {
	$params = $router->getParams();
	$paramsOutput = var_export($params, true);

	$randomParam1 = rand(1000, 9999);
	$randomParam2 = rand(1000, 9999);

	$content =
<<<HTML
<h1>Parameters test page</h1>
<p><a href="test/{$params['param1']}/{$params['param2']}/{$params['param3']}/child/{$randomParam1}/{$randomParam2}">Child parameters test page</a>.</p>
<pre>
{$paramsOutput}
</pre>
HTML;

	return [
		'title' => 'Parameters test page',
		'content' => $content
	];
};
