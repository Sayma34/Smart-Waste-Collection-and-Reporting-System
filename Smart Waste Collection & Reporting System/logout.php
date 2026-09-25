<?php
session_start();
session_destroy();
header("Location: login.php");
exit();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Your Page Title</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
