<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] != 'collector') {
    header("Location: login.php");
    exit();
}
<div class="card">
                <h2>Vehicle Status Report</h2>
                <?php if ($vehicle): ?>
                    <h3>Submit Vehicle Status Report</h3>
                    <form method="post" action="">
                        <input type="hidden" name="vehicleID" value="<?php echo $vehicle['vehicleID']; ?>">
                        <div style="margin-bottom: 15px;">
                            <label for="description">Issue Description:</label><br>
                            <textarea id="description" name="description" placeholder="Provide detailed description of the issue..." required></textarea>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label for="vehicle_status">Vehicle Status:</label><br>
                            <select id="vehicle_status" name="vehicle_status" required>
                                <option value="Operational">Operational</option>
                                <option value="Under Maintenance">Under Maintenance</option>
                                <option value="Out of Service">Out of Service</option>
                            </select>
                        </div>
                        <input type="submit" name="submit_report" value="Submit Report">
                    </form>