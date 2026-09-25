<?php
session_start();
include 'db_connect.php';

// Only allow admin users
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$admin_name = htmlspecialchars($_SESSION['name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Dashboard</title>
<link rel="stylesheet" href="style.css" />
<style>
  body { background: #eef7f3; font-family: Arial, sans-serif; color: #23373a; }
  .container { max-width: 950px; margin: 30px auto; background: #fff; border-radius: 14px; box-shadow: 0 6px 22px rgba(83,169,117,0.13); padding: 28px 22px; }
  .welcome { margin-bottom: 32px; text-align: center; }
  .welcome h1 { margin-bottom: 6px; color: #228b56; font-size: 2.2em; }
  .welcome p { margin: 0; font-size: 1.12em; color: #17643b; }
  h2 { color: #169056; margin-top: 36px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 32px; }
  th, td { padding: 13px 12px; border-bottom: 1px solid #ddebe2; text-align: left; font-size: 16px; }
  th { background: #e7f5ef; color: #139a4b; }
  tr:hover td { background: #f0faf3; }
  .logout-btn {
    display: inline-block; min-width: 140px; background: #417c3b; color:#fff;
    text-align:center; border-radius:7px; padding:13px; font-size:18px;
    margin: 28px auto 0; text-decoration:none; cursor:pointer;
    box-shadow:0 2px 9px rgba(60,120,63,0.07); width: fit-content;
  }
  .logout-btn:hover { background: #28552e; }
  input[type="number"], input[type="text"], select { padding: 5px; }
  select[name="vehicleID"] { width: 200px; }
  form { margin:0; }
</style>
</head>
<body>
<div class="container">

  <div class="welcome">
    <h1>Welcome, <?= $admin_name ?></h1>
    <p>This is your <strong>Admin Dashboard</strong>.</p>
  </div>

  <!-- Areas -->
  <h2>Area Descriptions</h2>
  <table>
    <tr>
      <th>Area Name</th>
      <th>Postal Code</th>
      <th>Description</th>
      <th>Total Bins</th>
      <th>Action</th>
    </tr>
    <?php
    $areas = $conn->query("SELECT name, postalcode, description, total_bins FROM area");
    while ($area = $areas->fetch_assoc()):
    ?>
    <form method="post" action="update_area_bins.php">
      <tr>
        <td><?= htmlspecialchars($area['name']) ?></td>
        <td><?= htmlspecialchars($area['postalcode']) ?></td>
        <td><?= htmlspecialchars($area['description']) ?></td>
        <td>
          <input type="number" name="total_bins" value="<?= htmlspecialchars($area['total_bins']) ?>" min="0" required>
          <input type="hidden" name="name" value="<?= htmlspecialchars($area['name']) ?>">
        </td>
        <td><input type="submit" value="Update"></td>
      </tr>
    </form>
    <?php endwhile; ?>
  </table>

  <!-- Vehicles -->
  <h2>Vehicle Details</h2>
  <table>
    <tr>
      <th>Vehicle No</th>
      <th>Type</th>
      <th>Status</th>
      <th>Allocated To</th>
      <th>Last Issue Description</th>
    </tr>
    <?php
    $vehicleQuery = "
      SELECT v.vehicleNO, v.type, v.status, u.name AS allocatedName,
      (SELECT description FROM vehicle_reports WHERE vehicleID = v.vehicleID ORDER BY submitted_at DESC LIMIT 1) AS lastIssue
      FROM vehicle v
      LEFT JOIN collector c ON v.assigned_collectorID = c.userID
      LEFT JOIN user u ON c.userID = u.userID
    ";
    $vehicles = $conn->query($vehicleQuery);
    while ($veh = $vehicles->fetch_assoc()):
    ?>
    <tr>
      <td><?= htmlspecialchars($veh['vehicleNO']) ?></td>
      <td><?= htmlspecialchars($veh['type']) ?></td>
      <td><?= htmlspecialchars($veh['status']) ?></td>
      <td><?= htmlspecialchars($veh['allocatedName'] ?? 'N/A') ?></td>
      <td><?= htmlspecialchars($veh['lastIssue'] ?? 'No issues reported') ?></td>
    </tr>
    <?php endwhile; ?>
  </table>

  <!-- Reports -->
  <h2>User Reports</h2>
  <table>
    <tr>
      <th>Report ID</th>
      <th>User Name</th>
      <th>Area</th>
      <th>Description</th>
      <th>Status</th>
      <th>Created At</th>
      <th>Action</th>
    </tr>
    <?php
    $reportsQuery = "
      SELECT r.reportID, u.name AS userName, r.area, r.description, r.status, r.created_at
      FROM reports r
      JOIN user u ON r.userID = u.userID
      ORDER BY r.created_at DESC
    ";
    $reports = $conn->query($reportsQuery);
    while ($rep = $reports->fetch_assoc()):
    ?>
    <form method="post" action="update_report_status.php">
      <tr>
        <td><?= htmlspecialchars($rep['reportID']) ?></td>
        <td><?= htmlspecialchars($rep['userName']) ?></td>
        <td><?= htmlspecialchars($rep['area']) ?></td>
        <td><?= htmlspecialchars($rep['description']) ?></td>
        <td>
          <select name="status">
            <option value="Pending" <?= $rep['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="Accepted" <?= $rep['status'] === 'Accepted' ? 'selected' : '' ?>>Accepted</option>
          </select>
        </td>
        <td><?= htmlspecialchars($rep['created_at']) ?></td>
        <td>
          <input type="hidden" name="reportID" value="<?= $rep['reportID'] ?>">
          <input type="submit" value="Update">
        </td>
      </tr>
    </form>
    <?php endwhile; ?>
  </table>

  <!-- Collector Management -->
  <h2>Collector Management</h2>
  <table>
    <tr>
      <th>Collector Name</th>
      <th>Collector ID</th>
      <th>Area</th>
      <th>Daily Goal</th>
      <th>Vehicle</th>
      <th>Action</th>
    </tr>
    <?php
    $collectorsQuery = "
      SELECT c.userID, u.name, c.area, c.dailygoal
      FROM collector c
      JOIN user u ON c.userID = u.userID
    ";
    $collectors = $conn->query($collectorsQuery);

    $vehiclesList = $conn->query("SELECT vehicleID, vehicleNO, type FROM vehicle");
    $vehicleOptions = [];
    while ($veh = $vehiclesList->fetch_assoc()) {
        $vehicleOptions[] = $veh;
    }

    while ($collector = $collectors->fetch_assoc()):
        $assignedVehicleQuery = $conn->prepare("SELECT vehicleID, vehicleNO, type FROM vehicle WHERE assigned_collectorID = ?");
        $assignedVehicleQuery->bind_param("i", $collector['userID']);
        $assignedVehicleQuery->execute();
        $assignedVehicleResult = $assignedVehicleQuery->get_result();
        $assignedVehicle = $assignedVehicleResult->fetch_assoc();
        $assignedVehicleQuery->close();
    ?>
    <form method="post" action="update_collector.php">
      <tr>
        <td><?= htmlspecialchars($collector['name']) ?></td>
        <td><?= $collector['userID'] ?></td>
        <td><input type="text" name="area" value="<?= htmlspecialchars($collector['area']) ?>" required></td>
        <td><input type="number" name="dailygoal" value="<?= htmlspecialchars($collector['dailygoal']) ?>" min="0" required style="width: 75px;"></td>
        <td>
          <select name="vehicleID" required>
            <option value="">--Select Vehicle--</option>
            <?php foreach ($vehicleOptions as $vo): ?>
            <option value="<?= $vo['vehicleID'] ?>" <?= ($assignedVehicle && $assignedVehicle['vehicleID'] == $vo['vehicleID']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($vo['vehicleNO'] . ' (' . $vo['type'] . ')') ?>
            </option>
            <?php endforeach; ?>
          </select>
        </td>
        <td>
          <input type="hidden" name="userID" value="<?= $collector['userID'] ?>">
          <input type="submit" value="Update">
        </td>
      </tr>
    </form>
    <?php endwhile; ?>
  </table>

  <!-- User Fee & Fine Management -->
  <h2>User Fee & Fine Management</h2>
  <table>
    <tr>
      <th>User Name</th>
      <th>Paid Fee (Tk)</th>
      <th>Fine Status</th>
      <th>Fine Amount</th>
      <th>Assign Fine</th>
    </tr>
    <?php
    $usersQuery = "
      SELECT u.userID, u.name, c.payment, c.fine_status, c.fine_due
      FROM user u
      JOIN citizen c ON u.userID = c.userID
      WHERE u.role = 'user'
    ";
    $users = $conn->query($usersQuery);
    while ($user = $users->fetch_assoc()):
        $feeAmount = 0;
        if ($user['payment'] > 0) {
            $feeAmount = 1000;
            if ($user['fine_status'] === 'Unpaid') {
                $feeAmount += $user['fine_due'];
            }
        }
    ?>
    <form method="post" action="update_user_fine.php">
      <tr>
        <td><?= htmlspecialchars($user['name']) ?></td>
        <td><?= $feeAmount ?></td>
        <td><?= htmlspecialchars($user['fine_status']) ?></td>
        <td><?= htmlspecialchars($user['fine_due']) ?></td>
        <td>
          <input type="hidden" name="userID" value="<?= $user['userID'] ?>">
          <input type="checkbox" name="assignFine" value="200" <?= ($user['fine_due'] > 0) ? 'checked' : '' ?>>
          <label for="assignFine">200 Tk</label>
          <input type="submit" value="Update">
        </td>
      </tr>
    </form>
    <?php endwhile; ?>
  </table>

  <a href="logout.php" class="logout-btn">Logout</a>
</div>
</body>
</html>
