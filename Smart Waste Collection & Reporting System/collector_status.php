<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] != 'collector') {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];
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