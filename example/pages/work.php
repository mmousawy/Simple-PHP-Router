<?php

return function(&$router) {
	$tabs = $router->createMenu('work', [
		'prefix' => '<li class="tab"><a class="tab__link%active-class%" href="%link%">',
		'suffix' => '</a></li>',
		'activeClass' => 'tab__active'
	]);

	$content =
<<<HTML
<h1>Work</h1>
<ul class="tabs">{$tabs}</ul>
<p>Here you'll find some tabs to example work.</p>
HTML;

	return [
		'title' => 'Work',
		'content' => $content
	];
};
