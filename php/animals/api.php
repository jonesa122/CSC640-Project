<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/php_errors.log');
error_reporting(E_ALL);
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';

$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;

// GET /index.php?endpoint=animals → list animals
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_GET['id']) && ($_GET['action'] ?? '') !== 'search') {
    $result = $mysqli->query("SELECT * FROM animals");
    $animals = [];
    while ($row = $result->fetch_assoc()) {
        $animals[] = $row;
    }
    echo json_encode($animals);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $mysqli->prepare("SELECT * FROM animals WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $animal = $result->fetch_assoc();
        echo json_encode($animal);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Animal not found"]);
    }
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'search') {
    $query = "SELECT * FROM animals WHERE 1=1";
    $params = [];
    $types = '';

    $filters = ['species', 'gender', 'status', 'name', 'breed', 'age'];

    foreach ($filters as $filter) {
        if (!empty($_GET[$filter])) {
            $value = $_GET[$filter];

            // Normalize ENUM values to match database casing
            if (in_array($filter, ['gender', 'status'])) {
                $value = ucfirst(strtolower($value)); // e.g., "female" → "Female"
            }

            $query .= " AND $filter = ?";
            $params[] = $value;
            $types .= is_numeric($value) ? 'i' : 's';
        }
    }

    $stmt = $mysqli->prepare($query);

    if ($params) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();

    $result = $stmt->get_result();
    $animals = [];
    while ($row = $result->fetch_assoc()) {
        $animals[] = $row;
    }


    echo json_encode($animals);
    exit;

}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches) || !validate_jwt($matches[1])) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['name'], $data['species'], $data['breed'], $data['age'], $data['gender'], $data['status'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
        exit;
    }

    $arrival_date = date('Y-m-d');
    $stmt = $mysqli->prepare("INSERT INTO animals (name, species, breed, age, gender, arrival_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $data['age'] = intval($data['age']);
    $stmt->bind_param("sssisss", $data['name'], $data['species'], $data['breed'], $data['age'], $data['gender'], $arrival_date, $data['status']);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["error" => "Insert failed", "details" => $stmt->error]);
        exit;
    } else{
        echo json_encode(["success" => true, "id" => $stmt->insert_id]);
        exit;
    }
}   


if ($_SERVER['REQUEST_METHOD'] === 'PATCH' && $action === 'update' && $id) {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches) || !validate_jwt($matches[1])) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data || !is_array($data)) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid JSON input"]);
        exit;
    }

    $allowedFields = ['name', 'species', 'breed', 'age', 'gender', 'status'];
    $setClauses = [];
    $params = [];
    $types = '';

    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $setClauses[] = "$field = ?";
            $params[] = $field === 'age' ? intval($data[$field]) : $data[$field];
            $types .= $field === 'age' ? 'i' : 's';
        }
    }

    if (empty($setClauses)) {
        http_response_code(400);
        echo json_encode(["error" => "No valid fields provided for update"]);
        exit;
    }

    $query = "UPDATE animals SET " . implode(', ', $setClauses) . " WHERE id = ?";
    $params[] = $id;
    $types .= 'i';

    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["error" => "Prepare failed", "details" => $mysqli->error]);
        exit;
    }

    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["error" => "Update failed", "details" => $stmt->error]);
        exit;
    }

    echo json_encode(["success" => true, "updated_id" => $id]);
    exit;
}



if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && $action === 'delete' && $id) {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches) || !validate_jwt($matches[1])) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        exit;
    }

    $stmt = $mysqli->prepare("DELETE FROM animals WHERE id = ?");
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["error" => "Delete failed", "details" => $stmt->error]);
        exit;
    } else{
        echo json_encode(["success" => true, "deleted_id" =>$id]);
        exit;
    }
}



// Default fallback
http_response_code(404);
echo json_encode(["error" => "Invalid animals endpoint"]);
