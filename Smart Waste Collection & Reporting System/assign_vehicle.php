<?php
session_start();
include 'db_connect.php';

// Only admin users can update
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userID = isset($_POST['userID']) ? intval($_POST['userID']) : 0;
    $vehicleID = isset($_POST['vehicleID']) ? intval($_POST['vehicleID']) : 0;
    $area = isset($_POST['area']) ? trim($_POST['area']) : '';
    $dailygoal = isset($_POST['dailygoal']) ? intval($_POST['dailygoal']) : 0;

    if ($userID <= 0 || empty($area) || $dailygoal < 0) {
        $_SESSION['message'] = "<div class='error'>Invalid input data.</div>";
        header("Location: admin_dashboard.php");
        exit();
    }

    // Check if the vehicle is already assigned
    if ($vehicleID > 0) {
        $checkStmt = $conn->prepare("SELECT assigned_collectorID FROM vehicle WHERE vehicleID = ?");
        $checkStmt->bind_param("i", $vehicleID);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $currentAssignment = $result->fetch_assoc();
        $checkStmt->close();

        if ($currentAssignment && $currentAssignment['assigned_collectorID'] && $currentAssignment['assigned_collectorID'] != $userID) {
            $_SESSION['message'] = "<div class='error'>This vehicle is already assigned to another collector.</div>";
            header("Location: admin_dashboard.php");
            exit();
        }
    }

    // Clear previous vehicle assignment for this collector
    $clearStmt = $conn->prepare("UPDATE vehicle SET assigned_collectorID = NULL WHERE assigned_collectorID = ?");
    $clearStmt->bind_param("i", $userID);
    $clearStmt->execute();
    $clearStmt->close();

    // Assign new vehicle
    if ($vehicleID > 0) {
        $assignStmt = $conn->prepare("UPDATE vehicle SET assigned_collectorID = ? WHERE vehicleID = ?");
        $assignStmt->bind_param("ii", $userID, $vehicleID);
        $assignStmt->execute();
        $assignStmt->close();
    }

    // Update collector details
    $updateStmt = $conn->prepare("UPDATE collector SET area = ?, dailygoal = ? WHERE userID = ?");
    $updateStmt->bind_param("sii", $area, $dailygoal, $userID);
    if ($updateStmt->execute()) {
        $_SESSION['message'] = "<div class='success'>Collector updated successfully!</div>";
    } else {
        $_SESSION['message'] = "<div class='error'>Error updating collector.</div>";
    }
    $updateStmt->close();

    $conn->close();
    header("Location: admin_dashboard.php");
    exit();
}

$conn->close();
?>