<?php
// dbconfig.php - databaseforbindelse

$dbHost = 'localhost';          // Typisk localhost, medmindre din DB ligger et andet sted
$dbName = 'formueguiden';       // Dit databasenavn
$dbUser = 'din_db_bruger';      // Dit databasebrugernavn
$dbPass = 'dit_db_password';    // Dit databaseadgangskode

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Databaseforbindelse fejlede: " . $e->getMessage());
}
?>
