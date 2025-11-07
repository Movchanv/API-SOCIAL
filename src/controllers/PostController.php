<?php
require_once 'config.php';

class PostController {

    public function createPost($data, $userId) {
        global $pdo;

        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $data['title'], $data['content']]);
        echo json_encode(['message' => 'Post created']);
    }

    public function getPosts($page = 1, $limit = 10, $userId = null) {
        global $pdo;

        $offset = ($page - 1) * $limit;

        $query = "SELECT posts.*, users.name as author FROM posts JOIN users ON posts.user_id = users.id";

        if ($userId !== null) {
            $query .= " WHERE posts.user_id = :userId";
        }

        $query .= " ORDER BY posts.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($query);

        if ($userId !== null) {
            $stmt->bindValue(':userId', (int)$userId, PDO::PARAM_INT);
        }

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countQuery = "SELECT COUNT(*) as total FROM posts";
        if ($userId !== null) $countQuery .= " WHERE user_id = :userId";
        $totalStmt = $pdo->prepare($countQuery);
        if ($userId !== null) $totalStmt->bindValue(':userId', (int)$userId, PDO::PARAM_INT);
        $totalStmt->execute();
        $totalPosts = $totalStmt->fetch()['total'];
        $totalPages = ceil($totalPosts / $limit);

        echo json_encode([
            'current_page' => $page,
            'per_page' => $limit,
            'total_posts' => (int)$totalPosts,
            'total_pages' => $totalPages,
            'posts' => $posts
        ]);
    }



    public function likePost($postId, $userId) {
        global $pdo;

        $stmt = $pdo->prepare("SELECT * FROM likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$postId, $userId]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['message' => 'Already liked']);
            return;
        }

        $stmt = $pdo->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
        $stmt->execute([$postId, $userId]);
        echo json_encode(['message' => 'Post liked']);
    }
}
