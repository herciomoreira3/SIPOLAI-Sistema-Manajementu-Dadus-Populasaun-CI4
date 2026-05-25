<?php
$mysqli = new mysqli("localhost", "root", "root");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
echo "Connected successfully with blank password.\n";
if ($mysqli->query("CREATE DATABASE IF NOT EXISTS sipolai_db")) {
    echo "Database created successfully\n";
} else {
    echo "Error creating database: " . $mysqli->error . "\n";
}
$mysqli->close();
?>
