<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];
$name = htmlspecialchars($_SESSION['name']);

$report_message = "";

// Handle new report submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_report'])) {
    $area = $_POST['area'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO reports (userID, area, description, status) VALUES (?, ?, ?, 'Pending')");
    $stmt->bind_param("iss", $userID, $area, $description);
    if ($stmt->execute()) {
        $report_message = "Report submitted successfully!";
    } else {
        $report_message = "Error submitting report.";
    }
    $stmt->close();
}

// Fetch user's reports
$reports = $conn->query("SELECT reportID, area, description, status, created_at FROM reports WHERE userID = $userID ORDER BY created_at DESC");

// Fetch fee and fine details
$feeRes = $conn->query("SELECT payment, fine_status, fine_due FROM citizen WHERE userID = $userID");
$fee = $feeRes ? $feeRes->fetch_assoc() : ['payment' => 0, 'fine_status' => 'No', 'fine_due' => 0];

// Calculate total monthly fee to show (base 1000 + fine if unpaid)
$total_fee = 1000;
if ($fee['fine_status'] === 'Unpaid' && $fee['fine_due'] > 0) {
    $total_fee += $fee['fine_due'];
}

// Handle fee payment update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_fee'])) {
    $payment = isset($_POST['feePaid']) ? 1 : 0;
    $fine_status = ($payment == 1) ? 'No' : $fee['fine_status'];
    $fine_due = ($payment == 1) ? 0 : $fee['fine_due'];

    $stmt = $conn->prepare("UPDATE citizen SET payment = ?, fine_status = ?, fine_due = ? WHERE userID = ?");
    $stmt->bind_param("isii", $payment, $fine_status, $fine_due, $userID);
    $stmt->execute();
    $stmt->close();

    // Refresh page to reflect updated fee/fine status
    header("Location: user.php");
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>User Dashboard</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        body {
            background: linear-gradient(135deg, #e6f2e6 0%, #c1e1c1 100%);
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 700px;
            margin: 44px auto;
            background: rgba(255,255,255,0.98);
            border-radius: 15px;
            padding: 38px 26px 32px 26px;
            box-shadow: 0 6px 32px rgba(60,120,63,0.13);
        }
        h1 {
            color: #307a48;
            text-align: center;
            font-weight: 700;
            margin-bottom: 12px;
            font-size: 32px;
        }
        .message-success {
            color: green;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }
        .report-section {
            background: #f8fcf7;
            border-radius: 12px;
            padding: 26px 18px;
            margin-bottom: 22px;
            box-shadow: 0 1px 8px rgba(60,120,63,0.06);
        }
        label, textarea, input {
            display: block;
            width: 100%;
            margin-bottom: 15px;
            font-size: 16px;
        }
        textarea {
            resize: vertical;
        }
        input[type="submit"] {
            background: #228b56;
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 6px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 22px;
            margin-bottom: 22px;
        }
        th, td {
            padding: 12px 10px;
            border: 1px solid #c3dec1;
            text-align: left;
            font-size: 15px;
        }
        th {
            background: #e3f0e1;
            color: #317030;
        }
        .logout-btn {
            display: block;
            margin: 25px auto 0 auto;
            width: 140px;
            padding: 14px;
            background: #417c3b;
            color: white;
            text-align: center;
            border-radius: 7px;
            text-decoration: none;
            font-size: 18px;
            box-shadow: 0 2px 9px rgba(60,120,63,0.07);
        }
        .logout-btn:hover {
            background: #28552e;
        }
        @media (max-width: 600px) {
            .container { padding: 15px; }
            input[type=submit], .logout-btn { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo $name; ?>!</h1>

        <?php if ($report_message): ?>
            <p class="message-success"><?php echo $report_message; ?></p>
        <?php endif; ?>




        <h2>Your Fee & Fine Status</h2>
        <form method="post" action="" style="margin-bottom: 0;">
        <table>
            <tr>
                <th>Monthly Fee Paid (Tk)</th>
                <td><?php echo htmlspecialchars($total_fee); ?></td>
            </tr>
            <tr>
                <th>Fine Status</th>
                <td>
                    <?php
                    if ($fee['fine_status'] === 'Unpaid' && $fee['fine_due'] > 0) {
                        echo "You have to pay Tk " . htmlspecialchars($fee['fine_due']) . " late fee!";
                    } else {
                        echo "You don't have any fine!";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>I have paid this month's fee</th>
                <td>
                    <input type="checkbox" id="feePaid" name="feePaid" value="1" <?php if ($fee['payment'] > 0) echo 'checked'; ?> />
                    <label for="feePaid" style="font-weight:normal; cursor:pointer;"> Payment Done</label>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:center;">
                    <input type="submit" name="pay_fee" value="Update Fee Status"
                    style="margin-top:8px; padding: 10px 30px; background:#228b56; color:#fff; border:none; border-radius: 5px; font-size:16px; cursor: pointer;" />
                </td>
            </tr>
        </table>
        </form>

        <div class="report-section">
            <h2>Submit a New Report</h2>
            <form method="post" action="">
                <label for="area">Area</label>
                <input type="text" id="area" name="area" required />

                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required></textarea>

                <input type="submit" name="submit_report" value="Submit Report" />
            </form>
        </div>  
        
                <h2>Your Reports</h2>
        <table>
            <thead>
                <tr>
                    <th>Report ID</th>
                    <th>Area</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($reports && $reports->num_rows > 0): ?>
                    <?php while ($report = $reports->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($report['reportID']); ?></td>
                            <td><?php echo htmlspecialchars($report['area']); ?></td>
                            <td><?php echo htmlspecialchars($report['description']); ?></td>
                            <td><?php echo htmlspecialchars($report['status']); ?></td>
                            <td><?php echo htmlspecialchars($report['created_at']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;">No reports found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>
