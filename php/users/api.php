<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';

$action = $_GET['action'] ?? null;

// REGISTER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'register') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['username'], $data['email'], $data['password'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
        exit;
    }

    $username = trim($data['username']);
    $email = trim($data['email']);
    $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password_hash);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "id" => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Account creation failed"]);
    }
    exit;
}

// LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['email'], $data['password'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing email or password"]);
        exit;
    }

    $email = trim($data['email']);
    $password = $data['password'];

    $stmt = $mysqli->prepare("SELECT id, username, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash'])) {
        $token = generate_jwt($user['id'], $user['username']);
        echo json_encode([
            "success" => true,
            "token" => $token
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["error" => "Invalid credentials"]);
    }
    exit;
}

http_response_code(404);
echo json_encode(["error" => "Unknown users action"]);
