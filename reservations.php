<?php include 'db.php'; ?>

<h2>Vehicle Reservations</h2>
<form method="post">
    <select name="vehicle_id">
        <?php
        $vehicles = $pdo->query("SELECT * FROM vehicles");
        foreach ($vehicles as $v) {
            echo "<option value='{$v['id']}'>{$v['make']} {$v['model']}</option>";
        }
        ?>
    </select>
    <input type="datetime-local" name="start" required>
    <input type="datetime-local" name="end" required>
    <button type="submit">Reserve</button>
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $pdo->prepare("INSERT INTO reservations (user_id, vehicle_id, start_time, end_time, status) VALUES (1, ?, ?, ?, 'pending')");
    $stmt->execute([$_POST['vehicle_id'], $_POST['start'], $_POST['end']]);
    echo "<p>Reservation made!</p>";
}

$stmt = $pdo->query("SELECT * FROM reservations");
echo "<ul>";
while ($row = $stmt->fetch()) {
    echo "<li>Vehicle ID {$row['vehicle_id']} reserved from {$row['start_time']} to {$row['end_time']}</li>";
}
echo "</ul>";
?>