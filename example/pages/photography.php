<?php

return function(&$router) {
	$tabs = $router->createMenu('work', [
		'prefix' => '<li class="tab"><a class="tab__link%active-class%" href="%link%">',
		'suffix' => '</a></li>',
		'activeClass' => 'tab__link--active'
	]);

	$menu = $router->createMenu('work/photography', [
		'prefix' => '<li class="sidenav__item%active-class%"><a class="sidenav__link" href="%link%">',
		'suffix' => '</a></li>',
		'activeClass' => 'sidenav__item--active'
	]);

	$content =
<<<HTML
<h1>Work / Photography</h1>
<ul class="tabs">{$tabs}</ul>
<ul class="sidenav">{$menu}</ul>
HTML;

	return [
		'title' => 'Photography',
		'content' => $content
	];
};
