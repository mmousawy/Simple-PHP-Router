<?php

return function(&$router) {
	$content =
<<<HTML
<h1>Simple PHP Router example</h1>
<p>This is an example website with a fully functional setup of the Simple PHP Router class.</p>
<p>Navigate throughout this website with the menu in the header.</p>
HTML;

	return [
		'title' => 'Simple PHP Router',
		'content' => $content
	];
};
