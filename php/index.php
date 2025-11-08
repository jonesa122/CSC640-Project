<?php
header('Content-Type: application/json');

// Basic router based on query parameter
$endpoint = $_GET['endpoint'] ?? null;

switch ($endpoint) {
    case 'animals':
        require_once __DIR__ . '/animals/api.php';
        break;
    case 'adoptions':
        require_once __DIR__ . '/adoptions/api.php';
        break;
    case 'users':
        require_once __DIR__ . '/users/api.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(["error" => "Unknown endpoint"]);
}
