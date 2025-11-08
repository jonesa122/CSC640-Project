<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/vendor/autoload.php';

$JWT_SECRET = 'your_secret_key_here';

function generate_jwt($user_id, $username) {
    global $JWT_SECRET;
    $payload = [
        'iss' => 'your-app',
        'aud' => 'your-app',
        'iat' => time(),
        'exp' => time() + 3600,
        'sub' => $user_id,
        'username' => $username
    ];
    return JWT::encode($payload, $JWT_SECRET, 'HS256');
}

function validate_jwt($token) {
    global $JWT_SECRET;
    try {
        return JWT::decode($token, new Key($JWT_SECRET, 'HS256'));
    } catch (Exception $e) {
        return null;
    }
}
