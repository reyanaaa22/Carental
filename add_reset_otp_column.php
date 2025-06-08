<?php
require_once('db.php');

try {
    $conn = new PDO("mysql:host=localhost;dbname=ocrms", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Add reset_otp column if it doesn't exist
    $sql = "ALTER TABLE tblusers ADD COLUMN IF NOT EXISTS reset_otp VARCHAR(6) DEFAULT NULL";
    $conn->exec($sql);
    echo "Successfully added reset_otp column to tblusers table.";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 