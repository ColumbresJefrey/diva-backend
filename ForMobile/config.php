    
<?php
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";
$password = "";
$database = "backend_diva";

$conn = new mysqli($servername, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>