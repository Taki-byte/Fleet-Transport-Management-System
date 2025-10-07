<?php
session_start();

// Check if driver is logged in
if (!isset($_SESSION['driver_id']) || empty($_SESSION['driver_id'])) {
    header("Location: driver_login.php");
    exit;
}

// Safe driver ID
$driver_id = (int) $_SESSION['driver_id'];

include 'db.php';

// Handle "Arrive" button click
if (isset($_POST['arrive']) && isset($_POST['trip_id'])) {
    $trip_id = $_POST['trip_id'];
    $updateTrip = $pdo->prepare("UPDATE trips SET status = 'Completed' WHERE id = ?");
    $updateTrip->execute([$trip_id]);
    header("Location: driver_user.php");
    exit;
}

// Handle chat submission
if (isset($_POST['chat_message']) && !empty(trim($_POST['chat_message']))) {
    $msg = trim($_POST['chat_message']);
    $stmt = $pdo->prepare("INSERT INTO driver_chat (driver_id, message, sender) VALUES (?, ?, 'driver')");
    $stmt->execute([$driver_id, $msg]);
    header("Location: driver_user.php");
    exit;
}

// Mark the driver as online
$updateStatus = $pdo->prepare("UPDATE drivers SET status = 'online' WHERE id = ?");
$updateStatus->execute([$driver_id]);

// Fetch driver info
$stmt = $pdo->prepare("SELECT * FROM drivers WHERE id = ?");
$stmt->execute([$driver_id]);
$driver = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch latest assigned trip
$tripStmt = $pdo->prepare("SELECT * FROM trips WHERE driver_id = ? AND status != 'Completed' ORDER BY trip_start DESC LIMIT 1");
$tripStmt->execute([$driver_id]);
$trip = $tripStmt->fetch(PDO::FETCH_ASSOC);

// Fetch vehicle info if trip exists
$vehicle = null;
if ($trip) {
    $vehicleStmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
    $vehicleStmt->execute([$trip['vehicle_id']]);
    $vehicle = $vehicleStmt->fetch(PDO::FETCH_ASSOC);
}

// Set photo path
$photoPath = !empty($driver['photo']) ? 'uploads/' . htmlspecialchars($driver['photo']) : 'uploads/default.png';

// Set Google Maps iframe URL if trip exists
$mapSrc = "";
if ($trip) {
    $origin = urlencode($trip['origin']);
    $destination = urlencode($trip['destination']);
    $mapSrc = "https://www.google.com/maps?q={$origin}+to+{$destination}&output=embed";
}

// Fetch chat messages
$chatStmt = $pdo->prepare("SELECT * FROM driver_chat WHERE driver_id = ? ORDER BY created_at ASC");
$chatStmt->execute([$driver_id]);
$chats = $chatStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Dashboard | Fleet & Transport Management</title>
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
            background: linear-gradient(90deg,#0d6efd,black);
            color: white;
            text-align: center;
            width: 100%;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .logout {
            position: absolute;
            right: 20px;
        }

        .logout a {
            color: white;
            background: red;
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
        }

        .logout a:hover {
            background: darkred;
        }

        .window {
            display: flex;
            align-items: center;
            background-color: #91BAd6;
            border-radius: 8px;
            margin-top: 50px;
            padding: 30px 20px;
            width: 70%;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            flex-wrap: wrap;
            position: relative;
        }

        .window img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid white;
            object-fit: cover;
            margin-right: 20px;
        }

        .vehicle-info {
            position: absolute;
            margin-left: 1150px;
            top: 30px;
            width: 140px;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .driver-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .driver-name {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .status {
            font-size: 18px;
            color: green;
            font-weight: bold;
            margin-bottom: 10px;
        }

        iframe {
            width: 72%;
            height: 350px;
            border: 2px solid #000;
            border-radius: 10px;
            margin-top: 20px;
        }

        .trip-details {
            width: 71%;
            background-color: #e2e2e2;
            padding: 15px;
            border-radius: 8px;
            margin-top: 5px;
            font-size: 16px;
        }

        .arrive-btn {
            margin-top: 10px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .arrive-btn:hover {
            background-color: #084298;
        }

        .no-trip {
            font-size: 18px;
            color: red;
            margin-top: 20px;
        }

        .chat-box {
            width: 70%;
            background: #fff;
            margin-top: 20px;
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
            width: 70%;
            margin-top: 10px;
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

        @media(max-width:650px) {
            iframe, .trip-details, .vehicle-info, .chat-box, .chat-form {
                width: 90%;
                position: static;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="title">
        <h1>Fleet & Transport Management System</h1>
        <div class="logout"><a href="logout.php">Logout</a></div>
    </div>

    <div class="window">
        <?php if ($vehicle): ?>
            <div class="vehicle-info">
                <?= htmlspecialchars($vehicle['make']) ?> <?= htmlspecialchars($vehicle['model']) ?>
                <p>Plate: <?= htmlspecialchars($vehicle['plate_number']) ?></p>
            </div>
        <?php endif; ?>

        <img src="<?= $photoPath ?>" alt="Driver Photo">

        <div class="driver-info">
            <div class="driver-name"><?= htmlspecialchars($driver['name']) ?></div>
            <div class="status">
                <?php if ($trip): ?>🟠 On Going<?php else: ?>🟢 Online<?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($trip): ?>
        <iframe src="<?= $mapSrc ?>" allowfullscreen="" loading="lazy"></iframe>
        <div class="trip-details">
            <h3>Your Assigned Trip</h3>
            <p><strong>Origin:</strong> <?= htmlspecialchars($trip['origin']) ?></p>
            <p><strong>Destination:</strong> <?= htmlspecialchars($trip['destination']) ?></p>
            <form method="POST">
                <input type="hidden" name="trip_id" value="<?= $trip['id'] ?>">
                <button type="submit" name="arrive" class="arrive-btn">Arrived</button>
            </form>
        </div>
    <?php else: ?>
        <div class="no-trip">No trips assigned yet.</div>
    <?php endif; ?>

    <!-- Chat Box -->
    <div class="chat-box" id="chat-box">
        <?php foreach ($chats as $chat): ?>
            <div class="chat-msg <?= htmlspecialchars($chat['sender']) ?>">
                <?= htmlspecialchars($chat['message']) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Chat Form -->
    <form method="POST" class="chat-form">
        <input type="text" name="chat_message" placeholder="Type your message..." required>
        <button type="submit">Send</button>
        <!-- Manual Refresh Button -->
        <button type="button" onclick="location.reload()">Refresh</button>
    </form>
</body>
</html>
