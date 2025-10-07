<?php
include 'db.php';
$driversStmt = $pdo->query("SELECT * FROM drivers");
$drivers = $driversStmt->fetchAll(PDO::FETCH_ASSOC);

$ongoingStmt = $pdo->query("
    SELECT DISTINCT driver_id 
    FROM trips 
    WHERE status = 'Assigned' OR status = 'Ongoing'
");
$ongoingDrivers = $ongoingStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Fleet & Transport Management</title>
    <style>
        body {
            background-color: #f4f6f9;
            font-family: Arial, sans-serif;
            padding: 30px;
        }
        h1 {
            background: linear-gradient(90deg, #0d6efd, black);
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        th, td {
            padding: 14px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #0d6efd;
            color: white;
        }
        .status {
            font-weight: bold;
        }
        .online { 
            color: green; 
        }
        .offline { 
            color: gray; 
        }
        .ongoing { 
            color: orange; 
            font-weight: bold; 
        }
        a.driver-link { 
            text-decoration: none; 
            color: black; 
            font-weight: bold; 
        }
        a.driver-link:hover { 
            text-decoration: underline; 
        }
    </style>
</head>
<body>
<table>
    <tr>
        <th>Driver Name</th>
        <th>Status</th>
    </tr>
    <?php foreach ($drivers as $driver): ?>
        <?php
            $statusText = '';
            $statusClass = '';
            if (in_array($driver['id'], $ongoingDrivers)) {
                $statusText = '🟠 On Going';
                $statusClass = 'ongoing';
            } 
            elseif ($driver['status'] === 'online') {
                $statusText = '🟢 Online';
                $statusClass = 'online';
            } 
            else {
                $statusText = '⚫ Offline';
                $statusClass = 'offline';
            }
        ?>
        <tr>
            <td>
                <a class="driver-link" href="driver_profile.php?id=<?= $driver['id'] ?>">
                    <?= htmlspecialchars($driver['name']) ?>
                </a>
            </td>
            <td class="status <?= $statusClass ?>"><?= $statusText ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<script>
    setTimeout(() => {
        window.location.reload();
    }, 5000);
</script>
</body>
</html>
