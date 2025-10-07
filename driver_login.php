<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM drivers WHERE name = ?");
    $stmt->execute([$name]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['driver_id'] = $user['id'];
        $_SESSION['driver_name'] = $user['name'];
        header("Location: driver_user.php");
        exit;
    } else {
        $error = "Invalid name or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Login | Fleet & Transport Management</title>
    <style>
        body {
            background-color: #f4f6f9;
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .title {
            background: linear-gradient(90deg, #0d6efd, black);
            color: white;
            text-align: center;
            width: 100%;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bc_login {
            background-color: #187bcd;
            box-shadow: 0 6px 22px rgba(0,0,0,0.05);
            text-align: center;
            width: 400px;
            padding: 30px;
            border-radius: 10px;
            margin-top: 140px;
        }

        .signup {
            height: 40px;
            width: 90%;
            margin-bottom: 20px;
            border-radius: 5px;
            padding: 10px;
            font-size: 18px;
        }

        button.signup {
            background-color: #0d6efd;
            color: white;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }

        button.signup:hover {
            background-color: #0b5ed7;
        }

        .error {
            background-color: #ffdddd;
            color: #a00;
            padding: 8px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        a {
            color: black;
        }
    </style>
</head>
<body>
    <div class="title">
        <h1>Fleet & Transport Management System</h1>
    </div>

    <form method="post" class="bc_login">
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <div><input class="signup" type="text" name="name" placeholder="Name" required></div>
        <div><input class="signup" type="password" name="password" placeholder="Password" required></div>
        <div><button class="signup" type="submit">Login</button></div>
        <p>No account? <a href="driver_register.php">Register here!</a></p>
        <p><a href="forgot_pasword.php">Forgot Password?</a></p>

    </form>
</body>
</html>
