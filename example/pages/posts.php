<?php

return function(&$router) {
	$params = $router->getParams();

	if (!$params) {
		$content =
<<<HTML
<h1>Posts</h1>
<p>Here you'll find some posts with dynamic parameter routing.</p>
<ul>
	<li><a href="posts/test-post-1">Post one</a></li>
	<li><a href="posts/test-post-2">Post two</a></li>
	<li><a href="posts/test-post-3">Post three</a></li>
</ul>
HTML;
	} else {
		$postLocation = 'data/' . $params['id'] . '.json';

		if (!file_exists($postLocation)) {
			$router->resolve('404');
			return $router->currentPage;
		}

		$postData = json_decode(file_get_contents($postLocation));

		$content =
<<<HTML
<h1>{$postData->title}</h1>
<div class="post-content">{$postData->content}</div>
HTML;
	}

	return [
		'title' => 'Posts',
		'content' => $content
	];
};
