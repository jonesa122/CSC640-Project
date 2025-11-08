<?php
$mysqli = new mysqli("db", "user", "userpass", "myapp");

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}
