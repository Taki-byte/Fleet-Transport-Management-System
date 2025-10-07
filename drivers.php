<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_driver'])) {
    $driver_id = (int)($_POST['driver_id'] ?? 0);
    if ($driver_id > 0) {
        $del = $pdo->prepare("DELETE FROM drivers WHERE id = ?");
        $del->execute([$driver_id]);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>
<h2>Drivers</h2>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    th {
        background-color: #0d6efd;
        color: white;
    }
    img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }
    .card {
        margin-top: 15px;
        padding: 15px;
        background: #f4f6f9;
        border-radius: 12px;
    }
    button.remove-btn {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 6px 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
    }
    button.remove-btn:hover {
        background-color: #a71d2a;
    }
</style>
<div class="card">
<?php
$stmt = $pdo->query("SELECT * FROM drivers");

if ($stmt->rowCount() > 0) {
    echo "<table>";
    echo "<tr>
            <th>Photo</th>
            <th>Name</th>
            <th>Age</th>
            <th>Gender</th>
            <th>License Number</th>
            <th>Phone no.</th>
            <th>Status</th>
            <th>Action</th>
          </tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $photoPath = !empty($row['photo']) ? 'uploads/' . htmlspecialchars($row['photo']) : 'uploads/default.png';
        $statusIcon = ($row['status'] === 'online') ? '🟢' : '⚫';
        
    echo "<tr>
            <td><img src='$photoPath' alt=''></td>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['age']) . "</td>
            <td>" . htmlspecialchars($row['gender']) . "</td>
            <td>" . htmlspecialchars($row['license_number']) . "</td>
            <td>" . htmlspecialchars($row['phone']) . "</td>
            <td>$statusIcon</td>
            <td> <form method='post' onsubmit='return confirm(\"Are you sure you want to remove this driver?\");'>
            <input type='hidden' name='driver_id' value='" . (int)$row['id'] . "'>
            <button type='submit' name='remove_driver' class='remove-btn'>Remove</button>
            </form></td></tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No drivers registered yet.</p>";
}
?>
</div>

<script>
    setTimeout(() => {
        window.location.reload();
    }, 5000);
</script>
