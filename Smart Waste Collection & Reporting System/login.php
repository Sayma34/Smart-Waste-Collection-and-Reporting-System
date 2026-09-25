<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize email input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT userID, name, password, role FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($userID, $name, $hashed_password, $role);

    if ($stmt->fetch()) {
        // Verify password securely
        if (password_verify($password, $hashed_password)) {
            // Regenerate session ID to prevent fixation attacks
            session_regenerate_id(true);

            $_SESSION['userID'] = $userID;
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role;

            // Redirect based on user role
            if ($role === 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($role === 'collector') {
                header("Location: collector.php");
            } else { // regular user
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Login - Waste Collection System</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
    <form method="post" action="">
        <h2>Login</h2>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
