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
