<?php
require_once 'config.php';
use Firebase\JWT\JWT;

class AuthController {

    private $secret = "super_secret_key";

    public function register($data) {
        header('Content-Type: application/json');
        global $pdo;

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt->execute([$data['name'], $data['email'], $hash]);

        echo json_encode([
            'message' => 'User registered',
            'email' => $data['email'],
            'password' => $data['password']
        ]);
    }

    public function login($data) {
        header('Content-Type: application/json');
        global $pdo;

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch();

        if ($user && password_verify($data['password'], $user['password'])) {
            $payload = [
                'id' => $user['id'],
                'email' => $user['email'],
                'iat' => time(),
                'exp' => time() + 3600
            ];
            $jwt = JWT::encode($payload, $this->secret, 'HS256');
            echo json_encode(['token' => $jwt]);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid credentials']);
        }
    }
}
