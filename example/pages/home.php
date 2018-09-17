<?php
return function(&$router) {
	$randomParam1 = rand(1000, 9999);
	$randomParam2 = rand(1000, 9999);
	$randomParam3 = rand(1000, 9999);

	$params = $router->getParams();

	$content =
<<<HTML
<img class="spr-logo" src="assets/img/spr-logo.svg">
<p>This is an example website with a fully functional setup of the Simple PHP Router class.</p>
<p>Navigate through this website with the menu in the header.</p>
<p>Or try the <a href="test/{$randomParam1}/{$randomParam2}/{$randomParam3}">parameters test page</a>.</p>
HTML;

	return [
		'title' => 'Simple PHP Router',
		'content' => $content
	];
};
