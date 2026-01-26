<?php
header('Content-Type: application/json; charset=utf-8');
include_once("../../includes/connection.php");

if (!isset($connection) || !$connection instanceof PDO) {
  http_response_code(500);
  echo json_encode(["status"=>"error","message"=>"DB connection missing"]);
  exit;
}

if (!isset($_SESSION['user_id'])) {
  http_response_code(403);
  echo json_encode(["status"=>"error","message"=>"Not logged in"]);
  exit;
}

$meeting_code = $_POST['code'] ?? null;
if (!$meeting_code) {
  http_response_code(400);
  echo json_encode(["status"=>"error","message"=>"Missing meeting code"]);
  exit;
}

// Find meeting id
$stmt = $connection->prepare("SELECT id FROM meetings WHERE code = ?");
$stmt->execute([$meeting_code]);
$meeting = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$meeting) {
  http_response_code(404);
  echo json_encode(["status"=>"error","message"=>"Meeting not found"]);
  exit;
}

$stmt = $connection->prepare("DELETE FROM chat_messages WHERE meeting_id = ?");
$stmt->execute([(int)$meeting['id']]);

echo json_encode(["status"=>"success"]);
