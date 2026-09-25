<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] != 'collector') {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID']; #stores the logged in user
$name = htmlspecialchars($_SESSION['name']);

// Handle bins collected update submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_bins'])) {

    
    // Check collector status before updating bins
    $stmt = $conn->prepare("SELECT status FROM collector WHERE userID = ?");
    $stmt->bind_param("i", $userID);         
    $stmt->execute();
    $result = $stmt->get_result();
    $current_status = $result->fetch_assoc()['status'];
    $stmt->close();

    if ($current_status === 'On Work') {
        $new_bins_collected = intval($_POST['bins_collected']);
        $stmt = $conn->prepare("UPDATE collector SET binscollected = ? WHERE userID = ?");
        $stmt->bind_param("ii", $new_bins_collected, $userID);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: collector.php");
    exit();
}


// Handle  collector status update submission badge related

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {  #request form method post
    $new_status = $_POST['status'] === 'on-work' ? 'On Work' : 'On Leave';
    $stmt = $conn->prepare("UPDATE collector SET status = ?, badge = 'No Badge' WHERE userID = ?");

    $stmt->bind_param("si", $new_status, $userID);
    $stmt->execute();
    $stmt->close();
    header("Location: collector.php");
    exit();
}


// Handle vehicle status report submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_report'])) {
    $vehicleID = intval($_POST['vehicleID']);
    $description = trim($_POST['description']);
    $status = $_POST['vehicle_status'];

    if (!empty($description) && !empty($status)) {
        $stmt = $conn->prepare("INSERT INTO vehicle_reports (reportID, vehicleID, userID, description, status, submitted_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiiss",$reportID, $vehicleID, $userID, $description, $status);
        $stmt->execute();
        $stmt->close();

        // Update vehicle status in the vehicle table

        $stmt = $conn->prepare("UPDATE vehicle SET status = ? WHERE vehicleID = ?");
        $stmt->bind_param("si", $status, $vehicleID);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: collector.php");
    exit();
}

######Data Fletching To Display########


// Get collector summary including status

$stmt = $conn->prepare("SELECT dailygoal, binscollected, badge, status FROM collector WHERE userID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$collector = $result->fetch_assoc();
$stmt->close();

// Get assigned vehicle details using assigned_collectorID reference
$vehicle = null;
$stmt = $conn->prepare("SELECT vehicleID, vehicleNO, type, status FROM vehicle WHERE assigned_collectorID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$vehicle = $result->fetch_assoc();
$stmt->close();

// Get all vehicles' status for fleet overview
$stmt = $conn->prepare("SELECT vehicleID, vehicleNO, status FROM vehicle");
$stmt->execute();
$result = $stmt->get_result();
$all_vehicles = $result->fetch_all(MYSQLI_ASSOC); #result in associative array
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Collector Dashboard</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        body {
            background: linear-gradient(135deg, #e6f2e6 0%, #a5d6a7 100%), url('https://via.placeholder.com/1500x1000.png?text=Waste+Truck+Background') no-repeat center center fixed;
            background-size: cover;
            background-blend-mode: overlay;
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            animation: fadeIn 1.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(60, 120, 63, 0.3);
            padding: 20px;
            position: relative;
            border: 2px solid #81c784;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background: rgba(50, 125, 50, 0.1);
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .welcome-msg {
            text-align: center;
            color: #1b5e20;
            font-size: 20px;
            margin: 0;
            font-style: italic;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }
        .role-msg {
            text-align: center;
            color: #388e3c;
            font-size: 16px;
            margin: 5px 0;
            font-weight: 600;
        }
        .logout-btn {
            background: linear-gradient(90deg, #66bb6a, #2e7d32);
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(60, 120, 63, 0.2);
            text-decoration: none;
        }
        .logout-btn:hover {
            background: linear-gradient(90deg, #4caf50, #1b5e20);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(60, 120, 63, 0.3);
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .card {
            background: linear-gradient(135deg, #e8f5e9, #ffffff);
            box-shadow: 0 5px 15px rgba(60, 120, 63, 0.15);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(60, 120, 63, 0.25);
        }
        .badge {
            margin-top: 15px;
            padding: 10px 20px;
            border-radius: 25px;
            color: #ffffff;
            font-weight: bold;
            font-size: 18px;
            display: inline-block;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .badge.silver {
            background: linear-gradient(90deg, #C0C0C0, #A9A9A9);
        }
        .badge.blue {
            background: linear-gradient(90deg, #2196F3, #1976D2);
        }
        .badge.gold {
            background: linear-gradient(90deg, #FFD700, #FFA500);
        }
        .status {
            margin-top: 10px;
            padding: 8px 15px;
            background: #fff3e0;
            border-radius: 15px;
            color: #e65100;
            font-weight: bold;
            font-size: 16px;
            display: inline-block;
            text-transform: capitalize;
        }
        .status.on-work { background: #c8e6c9; color: #2e7d32; }
        .status.on-leave { background: #ffccbc; color: #d32f2f; }
        .status.operational { background: #c8e6c9; color: #2e7d32; }
        .status.under-maintenance { background: #ffecb3; color: #f57c00; }
        .status.out-of-service { background: #ffccbc; color: #d32f2f; }
        input[type="number"], select, textarea {
            padding: 10px;
            width: 100%;
            max-width: 300px;
            font-size: 16px;
            margin-bottom: 12px;
            border: 2px solid #81c784;
            border-radius: 8px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        input[type="number"]:focus, select:focus, textarea:focus {
            border-color: #4caf50;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }
        input[type="submit"] {
            background: linear-gradient(90deg, #66bb6a, #4caf50);
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        input[type="submit"]:hover {
            background: linear-gradient(90deg, #4caf50, #2e7d32);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(60, 120, 63, 0.3);
        }
        .disabled-form {
            opacity: 0.6;
            pointer-events: none;
        }
        .vehicle-list {
            text-align: left;
            padding: 0;
        }
        .vehicle-list li {
            margin-bottom: 10px;
        }
        @media (max-width: 600px) {
            .container { padding: 15px; margin: 10px auto; }
            .grid { grid-template-columns: 1fr; }
            .header { flex-direction: column; text-align: center; }
            .logout-btn { margin-top: 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <div class="welcome-msg">
                    <h1>Welcome, <?php echo $name; ?>!</h1>
                    <div class="role-msg">
                        <b>Your role:</b> Collector
                    </div>
                </div>
            </div>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
        

        <div class="grid">
            <div class="card">
                <h2>Collector Summary 👷</h2>
                <?php if ($collector): ?>
                    <p><strong>🎯Daily Goal:</strong> <?php echo $collector['dailygoal']; ?></p>
                    <p><strong> 💪Bins Collected Today:</strong> <?php echo $collector['binscollected']; ?></p>
                    <?php if (isset($collector['status']) && !empty($collector['status'])): ?>
                        <p><strong>Status:</strong> <span class="status <?php echo strtolower(str_replace(' ', '-', $collector['status'])); ?>"><?php echo $collector['status']; ?></span></p>
                    <?php endif; ?>
                    
                    <form method="post" action="">
                        <select name="status" required>
                            <option value="on-work" <?php echo (isset($collector['status']) && $collector['status'] === 'On Work') ? 'selected' : ''; ?>>On Work</option>
                            <option value="on-leave" <?php echo (isset($collector['status']) && $collector['status'] === 'On Leave') ? 'selected' : ''; ?>>On Leave</option>
                        </select>
                        <input type="submit" name="update_status" value="Update Status">
                    </form>
                    <?php
                    $binsCollected = $collector['binscollected'];
                    $dailyGoal = $collector['dailygoal'];
                    $badge = 'No Badge';

                    if (isset($collector['status']) && $collector['status'] === 'On Work') {
                        $binsCollected = $collector['binscollected'];
                        $dailyGoal = $collector['dailygoal'];
                        $badge = 'No Badge';
                    
                        if ($binsCollected >= $dailyGoal && $dailyGoal > 0) {
                            $badge = 'Gold Badge ⭐⭐⭐';
                        } elseif ($binsCollected >= 10) {
                            $badge = 'Blue Badge ⭐⭐';
                        } elseif ($binsCollected >= 5) {
                            $badge = 'Silver Badge ⭐';
                        }
                    
                        $stmt = $conn->prepare("UPDATE collector SET badge = ? WHERE userID = ?");
                        $stmt->bind_param("si", $badge, $userID);
                        $stmt->execute();
                        $stmt->close();

                    } else {
                        $badge = $collector['badge'] ?? 'No Badge';
                    }
                    
                    ?>

                    <?php if (isset($collector['status']) && $collector['status'] === 'On Work' && $badge !== 'No Badge'): ?>
                        <div class="badge <?php echo strtolower(str_replace(' ', '-', substr($badge, 0, strpos($badge, ' ')))); ?>">🏅 <?php echo $badge; ?></div>
                    <?php endif; ?>

                    <?php
                    $isOnWork = (isset($collector['status']) && $collector['status'] === 'On Work');
                    ?>

                   
                    <form method="post" action="" <?php echo !$isOnWork ? 'class="disabled-form"' : ''; ?>>
                        <label for="bins_collected">Enter Bins Collected Today:</label><br>
                        <input type="number" id="bins_collected" name="bins_collected" min="0" value="<?php echo htmlspecialchars($collector['binscollected'] ?? 0); ?>" required <?php echo !$isOnWork ? 'disabled' : ''; ?>><br>
                        <input type="submit" name="update_bins" value="Update" <?php echo !$isOnWork ? 'disabled' : ''; ?>>
                    </form>
                    <?php if (!$isOnWork): ?>
                        <p style="color: #d32f2f; font-weight: bold;">You must be on work to update bins collected.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>No summary found for your account.</p>
                <?php endif; ?>
            </div>
             
             
    


            <div class="card">  
                <h2>VEHICLE DETAILS 🚚</h2>
                <br>
                <h2>Assigned Vehicle</h2>
               
                <?php if ($vehicle): ?>
                    <p><strong>Vehicle No:</strong> <?php echo htmlspecialchars($vehicle['vehicleNO']); ?></p>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($vehicle['type']); ?></p>
                   
                   
                    <br>
                    <h3>Current Vehicle Details</h3>
                    <p><strong>Vehicle No:</strong> <?php echo htmlspecialchars($vehicle['vehicleNO']); ?></p>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($vehicle['type']); ?></p>
                    <p><strong>Status:</strong> <span class="status <?php echo strtolower(str_replace(' ', '-', $vehicle['status'])); ?>"><?php echo $vehicle['status']; ?></span></p>
                    <br>
                
                    <h3> 🚚🚛Fleet Status Overview</h3>
                    <ul class="vehicle-list">  
                        <?php foreach ($all_vehicles as $v): ?>
                            <li>
                                <strong><?php echo htmlspecialchars($v['vehicleNO']); ?>:</strong>
                                <span class="status <?php echo strtolower(str_replace(' ', '-', $v['status'])); ?>"><?php echo $v['status']; ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>


                <?php else: ?>
                    <p>No vehicle assigned to you yet</p>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2>Vehicle Status Report📊</h2>
                <?php if ($vehicle): ?>
                    <h3>Submit Vehicle Status Report</h3>
                    <form method="post" action="">
                        <input type="hidden" name="vehicleID" value="<?php echo $vehicle['vehicleID']; ?>">
                        <div style="margin-bottom: 15px;">
                            <label for="description">Issue Description:</label><br>
                            <textarea id="description" name="description" placeholder="Provide detailed description of the issue..." required></textarea>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label for="vehicle_status">Vehicle Status:</label><br>
                            <select id="vehicle_status" name="vehicle_status" required>
                                <option value="Operational">Operational</option>
                                <option value="Under Maintenance">Under Maintenance</option>
                                <option value="Out of Service">Out of Service</option>
                            </select> 
                        </div>
                        <input type="submit" name="submit_report" value="Submit Report">
                    </form>

                  
                <?php else: ?>
                    <p>No vehicle assigned to manage reports.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
