<?php
session_start();
include 'db.php';

if (isset($_SESSION['driver_id'])) {
    $updateStatus = $pdo->prepare("UPDATE drivers SET status = 'offline' WHERE id = ?");
    $updateStatus->execute([$_SESSION['driver_id']]);
}

session_unset();
session_destroy();

header("Location: driver_login.php");
exit;
?>
