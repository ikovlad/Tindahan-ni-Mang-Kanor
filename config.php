<?php
// config.php

// --- Database Connection ---
$servername = "localhost"; // Or your database server
$username = "root";        // Your database username
$password = "";            // Your database password
$dbname = "sari_sari_store"; // The database name from your .sql file

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Helper function for formatting currency ---
function format_currency($amount) {
    if (is_numeric($amount)) {
        return '₱' . number_format($amount, 2);
    }
    return '₱0.00';
}
?>
