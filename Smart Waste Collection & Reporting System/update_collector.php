<?php
include 'db_connect.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$userID = intval($_POST['userID']);
$vehicleID = intval($_POST['vehicleID']);
$area = trim($_POST['area']);
$dailygoal = intval($_POST['dailygoal']);

// Unassign any vehicle currently assigned to this collector
$clearStmt = $conn->prepare("UPDATE vehicle SET assigned_collectorID = NULL WHERE assigned_collectorID = ?");
$clearStmt->bind_param("i", $userID);
$clearStmt->execute();
$clearStmt->close();

// Assign selected vehicle, if any
if ($vehicleID) {
    $assignStmt = $conn->prepare("UPDATE vehicle SET assigned_collectorID = ? WHERE vehicleID = ?");
    $assignStmt->bind_param("ii", $userID, $vehicleID);
    $assignStmt->execute();
    $assignStmt->close();
}

// Update collector info
$updateCollector = $conn->prepare("UPDATE collector SET area = ?, dailygoal = ? WHERE userID = ?");
$updateCollector->bind_param("sii", $area, $dailygoal, $userID);
$updateCollector->execute();
$updateCollector->close();

header("Location: admin_dashboard.php");
exit();
?>
