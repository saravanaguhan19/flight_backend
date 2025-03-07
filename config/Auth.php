<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Load Composer dependencies

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    private static $secret_key = "your_secret_key"; 
    private static $algo = "HS256";
    
    public static function generateToken($user) {
        $payload = [
            "user_id" => $user['id'],
            "username" => $user['username'],
            "email" => $user['email'],
            "exp" => time() + (60 * 60) // 1 hour expiry
        ];
        return JWT::encode($payload, self::$secret_key, self::$algo);
    }

    public static function verifyToken() {
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return false;
        }

        $token = str_replace("Bearer ", "", $_SERVER['HTTP_AUTHORIZATION']);
        try {
            return JWT::decode($token, new Key(self::$secret_key, self::$algo));
        } catch (Exception $e) {
            return false;
        }
    }
}
