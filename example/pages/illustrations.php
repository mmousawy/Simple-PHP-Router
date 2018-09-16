<?php

return function(&$router) {
	$tabs = $router->createMenu('work', [
		'prefix' => '<li class="tab"><a class="tab__link%active-class%" href="%link%">',
		'suffix' => '</a></li>',
		'activeClass' => 'tab__link--active'
	]);

	$content =
<<<HTML
<h1>Work / Illustrations</h1>
<ul class="tabs">{$tabs}</ul>
<h2>Mountain landscape</h2>
<img src="assets/img/illustration1.jpg">
<h2>Polygonal Lion Head</h2>
<img src="assets/img/illustration2.jpg">

<p>Illustrations from <a href="https://www.freepik.com/free-photos-vectors/design">freepik.com</a></p>
HTML;

	return [
		'title' => 'illustrations',
		'content' => $content
	];
};
