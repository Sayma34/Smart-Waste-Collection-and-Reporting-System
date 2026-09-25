<?php
session_start();
include 'db_connect.php';

// Only admin users can update
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userID = intval($_POST['userID']);
    $dailygoal = intval($_POST['dailygoal']);

    $stmt = $conn->prepare("UPDATE collector SET dailygoal = ? WHERE userID = ?");
    $stmt->bind_param("ii", $dailygoal, $userID);
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Error updating daily goal.";
    }
}
$conn->close();
?>