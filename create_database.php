<?php
try {
    // Connect to MySQL without specifying database
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS web_otoproject";
    $pdo->exec($sql);
    
    echo "Database 'web_otoproject' created successfully!\n";
    
    // Test connection to the new database
    $pdo_test = new PDO('mysql:host=127.0.0.1;port=3306;dbname=web_otoproject', 'root', '');
    echo "Connection to 'web_otoproject' database successful!\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
