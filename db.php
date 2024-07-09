<?php
$host = 'sql104.infinityfree.com';
$dbname = 'if0_36869863_product_listing';
$username = 'if0_36869863';
$password = 'Al020901';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
