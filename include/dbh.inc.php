<?php
// usage: include_once(include/dbh.inc.php)
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "moviejacuzzi";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
mysqli_set_charset($conn, 'utf8mb4');;
?>