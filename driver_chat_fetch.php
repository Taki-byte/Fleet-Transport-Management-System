<?php
include 'db.php';

$driver_id = (int)($_GET['id'] ?? 0);

$chatStmt = $pdo->prepare("SELECT * FROM driver_chat WHERE driver_id = ? ORDER BY created_at ASC");
$chatStmt->execute([$driver_id]);
$chats = $chatStmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($chats);
