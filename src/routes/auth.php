<?php
require_once __DIR__ . '/../controllers/AuthController.php';

$auth = new AuthController();

$router->post('/register', function() use ($auth) {
    $data = json_decode(file_get_contents('php://input'), true);
    $auth->register($data);
});

$router->post('/login', function() use ($auth) {
    $data = json_decode(file_get_contents('php://input'), true);
    $auth->login($data);
});
