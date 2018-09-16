# Simple-PHP-Router
A quick solution for routing friendly URLs and dynamic parameters to pages.

View example project using Simple PHP Router: https://murtada.nl/projects/simple-php-router/example/

## Setting up a project
### 1. Initializing classes

```php
require '/lib/SimplePhpRouter.php';
require '/lib/Route.php';

$router = new Murtada\SimplePhpRouter('routes.json');

require 'template.php';
```

### 2. Creating a template

`template.php`:
```php
<!DOCTYPE html>
<html lang="en">
<head>
	<title><?= $router->currentPage->title; ?></title>
	<base href="<?= $router->baseUrl; ?>">
</head>
<body class="page-<?= $router->currentPage->slug; ?>">
<header>
	<div class="wrapper wrapper--row">
		<a href="<?= $router->baseUrl; ?>" class="header__title">Simple PHP Router</a>
		<ul><?= $router->createMenu(); ?></ul>
	</div>
</header>
<main>
	<div class="wrapper">
		<?= $router->currentPage->content; ?>
	</div>
</main>
<footer>
	<div class="wrapper">
		<p class="footer__text">Copyright &copy; 2018 Murtada al Mousawy</p>
		<p class="footer__text">This software is free for use under the MIT License</p>
	</div>
</footer>
</body>
</html>
```

### 3. Create pages

`pages/home.php`:

```php
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
```
`pages/posts.php`:
```php
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
```

## Example `routes.json` file
```javascript
{
	"home": {
		"title": "Home",
		"slug": "home",
		"route": [
			"home",
			""
		],
		"menu_route": "",
		"path": "pages/home.php"
	},
	"posts": {
		"title": "Posts",
		"slug": "posts",
		"route": "posts/:id",
		"path": "pages/posts.php"
	},
	"work": {
		"title": "Work",
		"slug": "work",
		"route": "work",
		"path": "pages/work.php",
		"children": {
			"photography": {
				"title": "Photography",
				"slug": "photography",
				"route": "photography",
				"menu_route": "photography/portraits",
				"path": "pages/photography.php",
				"children": {
					"portraits": {
						"title": "Portraits",
						"slug": "portraits",
						"route": "portraits",
						"path": "pages/photography-portraits.php"
					},
					"weddings": {
						"title": "Weddings",
						"slug": "weddings",
						"route": "weddings",
						"path": "pages/photography-weddings.php"
					}
				}
			},
			"illustrations": {
				"title": "Illustrations",
				"slug": "illustrations",
				"route": "illustrations",
				"path": "pages/illustrations.php"
			}
		}
	},
	"404": {
		"title": "Page not found",
		"slug": "404",
		"route": "*",
		"path": "pages/404.php",
		"hidden": true
	}
}
```
