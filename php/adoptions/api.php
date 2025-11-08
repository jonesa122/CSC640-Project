<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';

$action = $_GET['action'] ?? null;


// POST /index.php?endpoint=adoptions → submit adoption request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $animal_id = $data['animal_id'] ?? null;

    if ($animal_id) {
        $checkStmt = $mysqli->prepare("SELECT id FROM animals WHERE id = ?");
        $checkStmt->bind_param("i", $animal_id);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows === 0) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid animal_id: no such animal exists"]);
            exit;
        }
    }

    if (!isset($data['animal_id'], $data['adopter_name'], $data['adopter_phone'], $data['adopter_email'], $data['adopter_address'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
        exit;
    }

    $adoption_date = date('Y-m-d');
    $stmt = $mysqli->prepare("INSERT INTO adoptions (animal_id, adoption_date, adopter_name, adopter_phone, adopter_email, adopter_address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "isssss",
        $data['animal_id'],
        $adoption_date,
        $data['adopter_name'],
        $data['adopter_phone'],
        $data['adopter_email'],
        $data['adopter_address']
    );
    if($stmt->execute()){
        echo json_encode(["success" => true, "id" => $stmt->insert_id]);
        exit;
    } else{
        http_response_code(500);
        echo json_encode(["error" => "Adoption request failed", "details" => $stmt->error]);
        exit; 
    }
}

// GET /index.php?endpoint=adoptions&id=1 → get adoption request by ID
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $mysqli->prepare("SELECT * FROM adoptions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $adoption = $result->fetch_assoc();

    if ($adoption) {
        echo json_encode($adoption);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Adoption request not found"]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PATCH' && $action === 'update') {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(["error" => "Missing or invalid Authorization header"]);
        exit;
    }

    $decoded = validate_jwt($matches[1]);
    if (!$decoded) {
        http_response_code(401);
        echo json_encode(["error" => "Invalid or expired token"]);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $id = $_GET['id'] ?? null;

    if (!$id || !is_array($data)) {
        http_response_code(400);
        echo json_encode(["error" => "Missing adoption ID or invalid input"]);
        exit;
    }

    $allowedFields = ['adopter_name', 'adopter_email', 'adopter_phone', 'adopter_address', 'animal_id', 'status', 'adoption_date'];
    $setClauses = [];
    $params = [];
    $types = '';

    $animal_id = $data['animal_id'] ?? null;

    if ($animal_id) {
        $checkStmt = $mysqli->prepare("SELECT id FROM animals WHERE id = ?");
        $checkStmt->bind_param("i", $animal_id);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows === 0) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid animal_id: no such animal exists"]);
            exit;
        }
    }

    foreach ($allowedFields as $field) {
        if (array_key_exists($field, $data)) {
            $setClauses[] = "$field = ?";
            $params[] = $field === 'animal_id' ? intval($data[$field]) : $data[$field];
            $types .= $field === 'animal_id' ? 'i' : 's';
        }
    }

    if (empty($setClauses)) {
        http_response_code(400);
        echo json_encode(["error" => "No valid fields provided for update"]);
        exit;
    }

    $query = "UPDATE adoptions SET " . implode(', ', $setClauses) . " WHERE id = ?";
    $params[] = intval($id);
    $types .= 'i';

    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["error" => "Prepare failed", "details" => $mysqli->error]);
        exit;
    }

    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "updated_id" => $id]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to update adoption request", "details" => $stmt->error]);
    }
    exit;
}




// Default fallback
http_response_code(404);
echo json_encode(["error" => "Invalid adoptions endpoint"]);
