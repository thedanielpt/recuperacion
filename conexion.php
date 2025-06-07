<?php
$host='localhost';
$dbname='practicalmgsi';
$user='root';
$pass='root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $i) {
    echo "error de la conexión: " . $i->getMessage();
}
?>