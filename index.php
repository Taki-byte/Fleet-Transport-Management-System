<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fleet & Transport Management</title>
    <style>
        body {
            background-color: #f4f6f9;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .bc {
            background: linear-gradient(90deg, #0d6efd, black);
            color: white;
            padding: 1.5rem;
            text-align: center;
            height: 50px;
            margin-bottom: 40px;
        }
        .bc h1 {
            margin: 0;
        }
        .bc p {
            margin: 5px 0 0;
        }
        .container {
            display: flex;
            gap: 20px;
            padding: 20px;
        }

        .left-panel {
            width: 30%;
        }

        .right-panel {
            width: 105%;
            height: 100%;
            background: white;
            border: 2px solid black;
            border-radius: 10px;
            padding: 20px;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 6px 22px rgba(0,0,0,0.05);
            border: 2px solid black;
            margin-bottom: 20px;
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #0d6efd, darkblue);
        }

        .link {

            font-size: 30px;
            color: white;
    
            border-radius: 5px;
            text-decoration: none;
            padding: 35px 60px;
            display: inline-block;
        }

        .link2 {
            font-size: 30px;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            padding: 35px 32px;
            display: inline-block;
        }

        iframe {
            width: 100%;
            height: 600px;
            border: none;
        }


    </style>
</head>
<body>

    <div class="bc">
        <h1>Fleet & Transport Management System</h1>
    </div>

    <div class="container">
        <div class="left-panel">
            <div class="card">
                <a href="dashboard.php" target="contentFrame" class="link">Dashboard</a>
            </div>

            <div class="card">
                <a href="vehicles.php" target="contentFrame" class="link">Manage Vehicles</a>
            </div>

            <div class="card">
                <a href="drivers.php" target="contentFrame" class="link">Manage Drivers</a>
            </div>

            <div class="card">
                <a href="trips.php" target="contentFrame" class="link">Dispatch</a>
            </div>
        </div>

            <div class="right-panel">
                <iframe name="contentFrame" src="dashboard.php"></iframe>
            </div>
        </div>

</body>
</html>
