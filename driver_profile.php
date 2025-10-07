<?php
include 'db.php';
session_start();

$driver_id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM drivers WHERE id = ?");
$stmt->execute([$driver_id]);
$driver = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$driver) {
    echo "Driver not found.";
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['chat_message'])) {
    $msg = trim($_POST['chat_message']);
    $user = 'admin';
    $stmt = $pdo->prepare("INSERT INTO driver_chat (driver_id, sender, message) VALUES (?, ?, ?)");
    $stmt->execute([$driver_id, $user, $msg]);
}
$chatStmt = $pdo->prepare("SELECT * FROM driver_chat WHERE driver_id = ? ORDER BY created_at ASC");
$chatStmt->execute([$driver_id]);
$chats = $chatStmt->fetchAll(PDO::FETCH_ASSOC);
$photoPath = !empty($driver['photo']) ? 'uploads/' . htmlspecialchars($driver['photo']) : 'uploads/default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($driver['name']) ?> | Profile</title>
<style>
body { 
    font-family: Arial; 
    padding:20px; 
    background:#f4f6f9; 
    display: flex; 
    flex-direction: column; 
    align-items: center;
}

.profile { 
    background:white; 
    padding:20px; 
    border-radius:10px; 
    box-shadow:0 4px 8px rgba(0,0,0,0.1); 
    width:500px; 
    margin-bottom:20px;
    display: flex;
    align-items: center;
}

.profile img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 15px;
    border: 2px solid #ccc;
}

.profile-info h2 {
    margin: 0;
    font-size: 24px;
}

.profile-info p {
    margin: 4px 0;
}

.chat-box { 
    width:500px; 
    background: #fff; 
    margin-bottom:10px; 
    border-radius: 8px; 
    padding: 15px; 
    box-shadow: 0 2px 4px rgba(0,0,0,0.2); 
    max-height: 300px; 
    overflow-y: auto; 
}

.chat-msg { 
    margin-bottom: 10px; 
    padding: 6px 10px; 
    border-radius: 6px; 
}

.chat-msg.admin { 
    background: #0d6efd; 
    color: white; 
    text-align: left; 
}

.chat-msg.driver { 
    background: #d1e7dd; 
    color: black; 
    text-align: right; 
}

.chat-form { 
    width:500px; 
    display: flex; 
}

.chat-form input { 
    flex: 1; 
    padding: 10px; 
    border-radius: 6px; 
    border: 1px solid #ccc; 
}

.chat-form button { 
    padding: 10px 15px; 
    margin-left: 5px; 
    border: none; 
    border-radius: 6px; 
    background: #0d6efd; 
    color: white; 
    cursor: pointer; 
}

.chat-form button:hover { 
    background: #084298; 
}
</style>
</head>
<body>

<div class="profile">
    <img src="<?= $photoPath ?>" alt="Driver Photo">
    <div class="profile-info">
        <h2><?= htmlspecialchars($driver['name']) ?></h2>
        <p>Age: <?= htmlspecialchars($driver['age']) ?></p>
        <p>Gender: <?= htmlspecialchars($driver['gender']) ?></p>
        <p>License No: <?= htmlspecialchars($driver['license_number']) ?></p>
        <p>Phone: <?= htmlspecialchars($driver['phone']) ?></p>
    </div>
</div>

<div class="chat-box" id="chatBox">
    <?php foreach ($chats as $chat): ?>
        <div class="chat-msg <?= htmlspecialchars($chat['sender']) ?>">
            <?= htmlspecialchars($chat['message']) ?>
        </div>
    <?php endforeach; ?>
</div>

<form method="POST" class="chat-form">
    <input type="text" name="chat_message" placeholder="Type your message..." required>
    <button type="submit">Send</button>
    <button type="button" onclick="location.reload();">Refresh</button>
</form>

</body>
</html>
