<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $role = $_POST['role'];

    // Check if email already exists
    $check = $conn->prepare("SELECT userID FROM user WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Email already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO user (name, password, email, phone, address, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $password, $email, $phone, $address, $role);

        if ($stmt->execute()) {
            $userid = $stmt->insert_id;

            if ($role == 'admin') {
                $conn->query("INSERT INTO admin (userID) VALUES ($userid)");
            } else if ($role == 'collector') {
                $conn->query("INSERT INTO collector (userID, status, dailygoal, binscollected, area) VALUES ($userid, 'Inactive', 0, 0, '')");
            } else if ($role == 'user') {
                $conn->query("INSERT INTO citizen (userID, feedback, payment, fine_status, fine_due) VALUES ($userid, '', 0, 'No', 0)");
            }

            $_SESSION['userID'] = $userid;
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role;

            // Redirect according to role
            if ($role === 'admin') {
                header("Location: admin_dashboard.php");
            } else if ($role === 'collector') {
                header("Location: collector.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $error = "Signup failed! Please try again.";
        }
        $stmt->close();
    }
    $check->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Sign Up - Waste Collection System</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
    <form method="post" action="">
        <h2>Sign Up</h2>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <label>Name:</label><br>
        <input type="text" name="name" required><br><br>
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <label>Phone:</label><br>
        <input type="text" name="phone" required><br><br>
        <label>Address:</label><br>
        <input type="text" name="address" required><br><br>
        <label>Role:</label><br>
        <select name="role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
            <option value="collector">Collector</option>
        </select><br><br>
        <input type="submit" value="Sign Up">
    </form>
</body>
</html>
