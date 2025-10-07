<?php
include 'db.php';
session_start();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM drivers WHERE name = ?");
    $stmt->execute([$name]);
    $driver = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$driver) {
        $error = "Driver not found!";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $updateStmt = $pdo->prepare("UPDATE drivers SET password = ? WHERE id = ?");
        $updateStmt->execute([$hashed_password, $driver['id']]);
        $success = "Password successfully updated! You can now <a href='driver_login.php'>login</a>.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Forgot Password | Fleet & Transport Management</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.reset-box {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    width: 400px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    text-align: center;
}

input {
    width: 90%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 6px;
    border: 1px solid #ccc;
}

button {
    padding: 10px 20px;
    border-radius: 6px;
    border: none;
    background: #0d6efd;
    color: white;
    cursor: pointer;
}

button:hover {
    background: #084298;
}

.error {
    background: #ffdddd;
    color: #a00;
    padding: 8px;
    border-radius: 5px;
    margin-bottom: 10px;
}

.success {
    background: #ddffdd;
    color: #070;
    padding: 8px;
    border-radius: 5px;
    margin-bottom: 10px;
}

#matchMessage {
    font-size: 14px;
    margin-bottom: 10px;
}
</style>
</head>
<body>

<div class="reset-box">
    <h2>Forgot Password</h2>
    <?php if ($error) echo "<div class='error'>$error</div>"; ?>
    <?php if ($success) echo "<div class='success'>$success</div>"; ?>
    <?php if (!$success): ?>
    <form method="POST" onsubmit="return checkPasswords();">
        <input type="text" name="name" placeholder="Enter your name" required>
        <input type="password" id="new_password" name="new_password" placeholder="New password" required>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
        <div id="matchMessage"></div>
        <button type="submit">Reset Password</button>
    </form>
    <?php endif; ?>
</div>

<script>
const newPass = document.getElementById('new_password');
const confirmPass = document.getElementById('confirm_password');
const matchMessage = document.getElementById('matchMessage');

function checkPasswords() {
    if (newPass.value !== confirmPass.value) {
        matchMessage.textContent = "Passwords do not match!";
        matchMessage.style.color = "red";
        return false;
    }
    return true;
}

confirmPass.addEventListener('input', () => {
    if (newPass.value === confirmPass.value) {
        matchMessage.textContent = "Passwords match!";
        matchMessage.style.color = "green";
    } else {
        matchMessage.textContent = "Passwords do not match!";
        matchMessage.style.color = "red";
    }
});
</script>
</body>
</html>
