<?php
echo "<h2>XAMPP PHP Configuration Check</h2>";

echo "<h3>PHP Installation Path:</h3>";
echo PHP_BINARY . "<br><br>";

echo "<h3>PHP Version:</h3>";
echo PHP_VERSION . "<br><br>";

echo "<h3>php.ini Location:</h3>";
echo php_ini_loaded_file() . "<br><br>";

echo "<h3>Extension Directory:</h3>";
echo ini_get('extension_dir') . "<br><br>";

echo "<h3>MySQL Extensions:</h3>";
$extensions = [
    'pdo',
    'pdo_mysql',
    'mysqli'
];

foreach ($extensions as $ext) {
    echo $ext . ": " . (extension_loaded($ext) ? 'Loaded' : 'Not Loaded') . "<br>";
}
?>