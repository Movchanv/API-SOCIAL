<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    private $secret = "super_secret_key";

    public function handle() {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['message' => 'Token required']);
            exit;
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);

        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return $decoded->id;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid token']);
            exit;
        }
    }
}
