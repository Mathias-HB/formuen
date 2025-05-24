<?php
session_start();

// Tjek om brugeren er logget ind
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Her kan du hente brugerinfo fra DB, hvis du vil vise mere end email
require 'dbconfig.php';

try {
    $stmt = $pdo->prepare("SELECT email, subscription, newsletter FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Hvis bruger ikke findes i DB, log ud og send til login
        session_destroy();
        header('Location: login.php');
        exit;
    }
} catch (Exception $e) {
    die("Fejl ved hentning af brugerdata: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="da">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Min Profil | FormueGuiden</title>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f7f9fc;
    color: #153c5d;
    max-width: 600px;
    margin: 3rem auto;
    padding: 2rem;
    border-radius: 10px;
    background-color: #fff;
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
  }
  h1 {
    text-align: center;
    color: #0b1e3d;
    margin-bottom: 2rem;
  }
  .profile-info {
    font-size: 1.1rem;
    line-height: 1.6;
  }
  .profile-info strong {
    color: #0b1e3d;
  }
  .logout-btn {
    display: block;
    width: 100%;
    margin-top: 2rem;
    padding: 0.8rem;
    background-color: #0b1e3d;
    color: white;
    text-align: center;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 700;
  }
  .logout-btn:hover {
    background-color: #005f8a;
  }
</style>
</head>
<body>

<h1>Velkommen til din profil</h1>

<div class="profile-info">
  <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
  <p><strong>Abonnement:</strong> <?php echo htmlspecialchars(ucfirst($user['subscription'])); ?></p>
  <p><strong>Tilmeldt nyhedsbrev:</strong> <?php echo $user['newsletter'] ? 'Ja' : 'Nej'; ?></p>
</div>

<a href="logout.php" class="logout-btn">Log ud</a>

</body>
</html>
