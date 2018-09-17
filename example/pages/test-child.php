
<?php

return function(&$router) {
	$paramsOutput = var_export($router->getParams(), true);
	$parentParamsOutput = var_export($router->getParams(-1), true);

	$content =
<<<HTML
<h1>Child parameters test page</h1>
<p>Child params:</p>
<pre>
{$paramsOutput}
</pre>
<p>Parent params:</p>
<pre>
{$parentParamsOutput}
</pre>
HTML;

	return [
		'title' => 'Child parameters test page',
		'content' => $content
	];
};
