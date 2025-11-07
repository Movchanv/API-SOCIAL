<?php
require_once __DIR__ . '/../controllers/PostController.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

$postController = new PostController();
$authMiddleware = new AuthMiddleware();

$router->post('/posts', function() use ($postController, $authMiddleware) {
    $userId = $authMiddleware->handle();
    $data = json_decode(file_get_contents('php://input'), true);
    $postController->createPost($data, $userId);
});

$router->get('/posts', function() use ($postController, $authMiddleware) {
    $authMiddleware->handle();

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 10;

    $postController->getPosts($page, $perPage);
});

$router->post('/posts/like/{id}', function($id) use ($postController, $authMiddleware) {
    $userId = $authMiddleware->handle();
    $postController->likePost($id, $userId);
});
