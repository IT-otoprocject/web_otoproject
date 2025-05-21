<?php
echo "<h2>Checking MySQL Configuration</h2>";

echo "<h3>PHP Version:</h3>";
echo PHP_VERSION . "<br><br>";

echo "<h3>Loaded PHP Extensions:</h3>";
$loaded_extensions = get_loaded_extensions();
echo "PDO: " . (in_array('pdo', $loaded_extensions) ? 'YES' : 'NO') . "<br>";
echo "PDO MySQL: " . (in_array('pdo_mysql', $loaded_extensions) ? 'YES' : 'NO') . "<br>";
echo "MySQL/MySQLi: " . (in_array('mysqli', $loaded_extensions) ? 'YES' : 'NO') . "<br><br>";

echo "<h3>PHP Configuration File Location:</h3>";
echo php_ini_loaded_file() . "<br><br>";

echo "<h3>Testing MySQL Connection:</h3>";
try {
    $conn = new PDO("mysql:host=127.0.0.1;port=3306", "root", "");
    echo "MySQL Connection: SUCCESS<br>";
} catch(PDOException $e) {
    echo "MySQL Connection: FAILED<br>";
    echo "Error: " . $e->getMessage();
}
?>