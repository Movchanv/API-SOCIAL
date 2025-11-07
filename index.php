<?php
require_once __DIR__ . '/vendor/autoload.php';

$router = new \Bramus\Router\Router();

require_once __DIR__ . '/src/routes/auth.php';
require_once __DIR__ . '/src/routes/posts.php';

$router->run();
