<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "waste_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Your Page Title</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
