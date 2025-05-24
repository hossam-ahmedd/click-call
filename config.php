<?php
/* db.php
 * Central DB connection file.
 * Adjust the host, username, password, and database as needed.
 */

$host     = 'localhost';
$username = 'root';
$password = '';
$database = 'mobil_db';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
?>
