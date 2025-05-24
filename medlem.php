<?php
session_start();

// Tjek om brugeren er logget ind
if (!isset($_SESSION['user_id'])) {
    header('Location: logind.php');
    exit;
}

require 'dbconfig.php'; // Separat fil med database-credentials (valgfrit)

// Hvis du ikke har en dbconfig.php, kan du kopiere DB-koblingen fra logind.php direkte her

// Forbind til DB - brug samme metode som i logind.php:
try {
    $pdo = new PDO("mysql:host=localhost;dbname=formueguiden;charset=utf8", "db_bruger", "db_password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Databaseforbindelse fejlede: " . $e->getMessage());
}

// Hent brugerinfo ud fra session user_id
$stmt = $pdo->prepare("SELECT email, subscription FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // Hvis bruger ikke findes, log ud og send til log ind
    session_destroy();
    header('Location: logind.php');
    exit;
}

// Logout funktion
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: logind.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="da">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Medlemsside | FormueGuiden</title>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    max-width: 480px;
    margin: 3rem auto;
    padding: 2rem;
    background-color: #fff;
    color: #153c5d;
    border-radius: 10px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
  }
  h1 {
    text-align: center;
    color: #0b1e3d;
    margin-bottom: 2rem;
  }
  .info {
    font-size: 1.1rem;
    margin-bottom: 2rem;
  }
  .logout-btn {
    display: block;
    width: 100%;
    padding: 0.8rem;
    background-color: #0b1e3d;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    font-size: 1.1rem;
  }
  .logout-btn:hover {
    background-color: #005f8a;
  }
</style>
</head>
<body>

<h1>Velkommen til din medlemsportal</h1>

<div class="info">
  <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
  <p><strong>Abonnement:</strong> <?php 
    if ($user['subscription'] === 'basic') echo 'Basic';
    elseif ($user['subscription'] === 'premium') echo 'Premium';
    elseif ($user['subscription'] === 'pro') echo 'Pro';
    else echo 'Ukendt'; 
  ?></p>
</div>

<a href="?logout=true" class="logout-btn">Log ud</a>

</body>
</html>
