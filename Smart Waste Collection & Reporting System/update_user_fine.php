<?php
session_start();
include 'db_connect.php';

// Only allow admin users to access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userID = intval($_POST['userID']);
    if (isset($_POST['assignFine'])) {
        // Assign a fine of 200 Tk with unpaid status
        $stmt = $conn->prepare("UPDATE citizen SET fine_due = 200, fine_status = 'Unpaid' WHERE userID = ?");
    } else {
        // Remove fine (set due to 0 and status to No)
        $stmt = $conn->prepare("UPDATE citizen SET fine_due = 0, fine_status = 'No' WHERE userID = ?");
    }
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: admin_dashboard.php");
exit();
?>
