<?php
// dbconfig.php

$dbHost = 'localhost';          // Din databasehost
$dbName = 'formueguiden';       // Dit databasenavn
$dbUser = 'din_db_bruger';      // Dit databasebrugernavn
$dbPass = 'dit_db_password';    // Dit databasepassword

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Databaseforbindelse fejlede: " . $e->getMessage());
}
?>
