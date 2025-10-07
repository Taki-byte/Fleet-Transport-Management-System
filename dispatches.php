<?php include 'db.php'; ?>

<h2>Dispatch</h2>
<form method="post">
    <select name="reservation_id">
        <?php
        $res = $pdo->query("SELECT * FROM reservations WHERE status='pending'");
        foreach ($res as $r) {
            echo "<option value='{$r['id']}'>Reservation #{$r['id']}</option>";
        }
        ?>
    </select>
    <select name="driver_id">
        <?php
        $drivers = $pdo->query("SELECT * FROM drivers");
        foreach ($drivers as $d) {
            echo "<option value='{$d['id']}'>{$d['name']}</option>";
        }
        ?>
    </select>
    <button type="submit">Dispatch</button>
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $pdo->prepare("INSERT INTO dispatches (reservation_id, driver_id, dispatched_at) VALUES (?, ?, NOW())");
    $stmt->execute([$_POST['reservation_id'], $_POST['driver_id']]);

    $pdo->prepare("UPDATE reservations SET status='approved' WHERE id = ?")->execute([$_POST['reservation_id']]);

    echo "<p>Driver dispatched!</p>";
}
?>