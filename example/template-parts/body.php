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
