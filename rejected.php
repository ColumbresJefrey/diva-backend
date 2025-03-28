<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}	
// Fetch only rejected (cancelled) appointments from the database
$appointments = $conn->query("
    SELECT a.id, u.customer_name AS customer_name, s.stylist_name AS stylist_name, 
           a.appointment_date, a.appointment_time, a.service, a.status 
    FROM appointments a
    JOIN users u ON a.customer_id = u.id
    JOIN stylists s ON a.stylist_id = s.id
    WHERE a.status = 'Rejected'
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Rejected Appointments</title>
    <link rel="stylesheet" href="appointments.css">
    <?php include 'sidebar.php'; ?>
    <?php include 'notification.php'; ?>
</head>
<body>
    <div class="dashboard-container">
        <h1>Rejected Appointments</h1>
        <input type="text" id="searchAppointment" class="search-box" placeholder="Search Appointments...">

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Stylist</th>
                    <th>Date</th>
                    <th>Time</th> <!-- Added Time Column -->
                    <th>Service</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($appointments->num_rows === 0) : ?>
                    <tr><td colspan="6">No rejected appointments.</td></tr>
                <?php else : ?>
                    <?php while ($row = $appointments->fetch_assoc()) : ?>
                        <?php
                        // Format the appointment time (e.g., 12-hour format with AM/PM)
                        $time = DateTime::createFromFormat('H:i:s', $row['appointment_time']);
                        $formatted_time = $time->format('h:i A'); // Convert to 12-hour format
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']); ?></td>
                            <td><?= htmlspecialchars($row['customer_name']); ?></td>
                            <td><?= htmlspecialchars($row['stylist_name']); ?></td>
                            <td><?= htmlspecialchars($row['appointment_date']); ?></td>
                            <td><?= htmlspecialchars($formatted_time); ?></td> <!-- Displaying formatted Appointment Time -->
                            <td><?= htmlspecialchars($row['service']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
    // Search functionality
    document.getElementById("searchAppointment").addEventListener("keyup", function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll("tbody tr");

        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });

    // Function to toggle dropdown visibility
    function toggleDropdown(id) {
        let dropdown = document.getElementById(id);
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }

    // Close dropdown when clicking outside
    document.addEventListener("click", function(event) {
        let dropdowns = document.querySelectorAll(".dropdown-menu");
        dropdowns.forEach(dropdown => {
            if (!dropdown.contains(event.target)) {
                dropdown.style.display = "none";
            }
        });
    });
    </script>
</body>
</html>
