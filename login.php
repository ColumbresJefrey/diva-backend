<?php
session_start();
include 'config.php'; // Include database connection

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        // Prepare statement to fetch admin credentials
        $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $db_username, $db_password);
            $stmt->fetch();

            // Direct password comparison (no hashing)
            if ($password === $db_password) {
                // Set session variables consistently
                $_SESSION['admin_id'] = $id; // Corrected variable name
                $_SESSION['username'] = $db_username;
                $_SESSION['admin_logged_in'] = true;

                header("Location: dashboard.php"); // Redirect to the admin panel
                exit();
            } else {
                $error = "❌ Invalid username or password!";
            }
        } else {
            $error = "❌ Admin user not found!";
        }

        $stmt->close();
    } else {
        $error = "❌ Please enter both username and password!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIVA Login</title>
    <link rel="stylesheet" type="text/css" href="login.css">
</head>
<body>
    <div class="logo">
        <h1>D I V A</h1>
        <p>Achieve your style</p>
    </div>

    <div class="container">
        <form method="POST" action="login.php">
            <strong><label for="username">Username:</label></strong>
            <input type="text" id="username" name="username" required>

            <strong><label for="password">Password:</label></strong>
            <input type="password" id="password" name="password" required>

            <?php if (!empty($error)): ?>
            <center><p style="color: red; font-weight: bold;"><?php echo $error; ?></p></center>
            <?php endif; ?>

            <input type="submit" value="Log in">
        </form>
        <center><p>Forgot password? <a href="forgotpassword.php" style="color: blue; text-decoration: underline; display: inline;">Click here</a></p></center>
    </div>
</body>
</html>
