<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Trip Cost Entry</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<h2>💰 Add Costs to a Trip</h2>

<form method="post">
    <label>Select Trip</label>
    <select name="trip_id" class="form-control mb-3">
        <?php
        $trips = $pdo->query("SELECT trips.id, vehicles.plate_number, drivers.name
            FROM trips
            JOIN vehicles ON trips.vehicle_id = vehicles.id
            JOIN drivers ON trips.driver_id = drivers.id
            ORDER BY trips.id DESC");
        foreach ($trips as $t) {
            echo "<option value='{$t['id']}'>Trip #{$t['id']} - {$t['plate_number']} ({$t['name']})</option>";
        }
        ?>
    </select>

    <div class="row mb-3">
        <div class="col"><input type="number" step="0.01" name="fuel_cost" class="form-control" placeholder="Fuel Cost" required></div>
        <div class="col"><input type="number" step="0.01" name="tolls" class="form-control" placeholder="Tolls"></div>
        <div class="col"><input type="number" step="0.01" name="maintenance_share" class="form-control" placeholder="Maintenance"></div>
        <div class="col"><input type="number" step="0.01" name="driver_fee" class="form-control" placeholder="Driver Fee"></div>
    </div>

    <button type="submit" class="btn btn-success">Save Cost</button>
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $pdo->prepare("INSERT INTO cost_entries (trip_id, fuel_cost, tolls, maintenance_share, driver_fee)
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['trip_id'],
        $_POST['fuel_cost'],
        $_POST['tolls'],
        $_POST['maintenance_share'],
        $_POST['driver_fee']
    ]);
    echo "<div class='alert alert-success mt-3'>✅ Cost entry added!</div>";
}
?>

<h4 class="mt-5">Recent Cost Entries</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Trip ID</th>
            <th>Fuel</th>
            <th>Tolls</th>
            <th>Maintenance</th>
            <th>Driver</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $entries = $pdo->query("SELECT * FROM cost_entries ORDER BY id DESC LIMIT 10");
        foreach ($entries as $e) {
            $total = $e['fuel_cost'] + $e['tolls'] + $e['maintenance_share'] + $e['driver_fee'];
            echo "<tr>
                <td>{$e['trip_id']}</td>
                <td>{$e['fuel_cost']}</td>
                <td>{$e['tolls']}</td>
                <td>{$e['maintenance_share']}</td>
                <td>{$e['driver_fee']}</td>
                <td><strong>$total</strong></td>
            </tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>