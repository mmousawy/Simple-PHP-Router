<?php

// Always show errors
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../lib/SimplePhpRouter.php';
require_once '../lib/Route.php';

$router = new Murtada\SimplePhpRouter('routes.json');

require_once 'template-parts/header.php';
require_once 'template-parts/body.php';
require_once 'template-parts/footer.php';
