<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_vehicle'])) {
        $stmt = $pdo->prepare("
            INSERT INTO vehicles (make, model, plate_number, year, fuel_type, mileage, status)
            VALUES (?, ?, ?, 2020, 'Diesel', 0, 'Available')
        ");
        $stmt->execute([$_POST['make'], $_POST['model'], $_POST['plate']]);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    if (isset($_POST['change_status'])) {
        $vehicle_id = (int)($_POST['vehicle_id'] ?? 0);
        $new_status = trim($_POST['new_status'] ?? '');
        $upd = $pdo->prepare("UPDATE vehicles SET status = ? WHERE id = ?");
        $upd->execute([$new_status, $vehicle_id]);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    if (isset($_POST['remove_vehicle'])) {
        $vehicle_id = (int)($_POST['vehicle_id'] ?? 0);
        $del = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
        $del->execute([$vehicle_id]);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Vehicles</title>
<style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        background: #f4f6f9;
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #0d6efd;
    }

    form.add-form input, form.add-form button {
        padding: 10px;
        margin: 5px 2px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    form.add-form button {
        background-color: #0d6efd;
        color: white;
        border: none;
        cursor: pointer;
        transition: 0.3s;
    }

    form.add-form button:hover {
        background-color: #084298;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        margin-top: 20px;
    }

    th, td {
        padding: 12px 15px;
        text-align: center;
    }

    th {
        background-color: #0d6efd;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    select {
        padding: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
        cursor: pointer;
    }

    button.remove-btn {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
    }

    button.remove-btn:hover {
        background-color: #a71d2a;
    }

    @media(max-width: 700px) {
        table, thead, tbody, th, td, tr {
            display: block;
        }
        th {
            text-align: left;
        }
        td {
            text-align: left;
            border-bottom: 1px solid #eee;
            padding-left: 50%;
            position: relative;
        }
        td:before {
            position: absolute;
            top: 12px;
            left: 15px;
            width: 45%;
            white-space: nowrap;
            font-weight: bold;
        }
        td:nth-of-type(1):before { 
            content: "Brand"; 
        }
        td:nth-of-type(2):before { 
            content: "Model"; 
        }
        td:nth-of-type(3):before { 
            content: "Plate"; 
        }
        td:nth-of-type(4):before { 
            content: "Status"; 
        }
        td:nth-of-type(5):before { 
            content: "Actions";
        }
    }
</style>
</head>
<body>

<h2>Vehicles</h2>

<form method="post" class="add-form">
    <input type="text" name="make" placeholder="Brand" required>
    <input type="text" name="model" placeholder="Model" required>
    <input type="text" name="plate" placeholder="Plate Number" required>
    <button type="submit" name="add_vehicle">Add Vehicle</button>
</form>

<?php
$stmt = $pdo->query("SELECT id, make, model, plate_number, status FROM vehicles ORDER BY id DESC");
$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($vehicles) === 0) {
    echo "<p style='text-align:center; margin-top:20px;'>No vehicles added yet.</p>";
} else {
    echo "<table>";
    echo "<thead>
            <tr>
                <th>Brand</th>
                <th>Model</th>
                <th>Plate Number</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
          </thead>";
    echo "<tbody>";
    foreach ($vehicles as $vehicle) {
        $id = (int)$vehicle['id'];
        $status = htmlspecialchars($vehicle['status']);
        echo "<tr>";
        echo "<td>" . htmlspecialchars($vehicle['make']) . "</td>";
        echo "<td>" . htmlspecialchars($vehicle['model']) . "</td>";
        echo "<td>" . htmlspecialchars($vehicle['plate_number']) . "</td>";
        echo "<td>
                <form method='post' style='display:inline;'>
                    <input type='hidden' name='vehicle_id' value='{$id}'>
                    <input type='hidden' name='change_status' value='1'>
                    <select name='new_status' onchange='this.form.submit()'>
                        <option value='Available' " . ($status === 'Available' ? 'selected' : '') . ">Available</option>
                        <option value='Not Available' " . ($status === 'Not Available' ? 'selected' : '') . ">Not Available</option>
                    </select>
                </form>
              </td>";
        echo "<td>
                <form method='post' onsubmit='return confirm(\"Are you sure you want to remove this vehicle?\")'>
                    <input type='hidden' name='vehicle_id' value='{$id}'>
                    <button type='submit' name='remove_vehicle' class='remove-btn'>Remove</button>
                </form>
              </td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
}
?>
</body>
</html>
