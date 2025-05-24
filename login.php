<?php
session_start();
require 'dbconfig.php';  // Inkluder din databaseforbindelse

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email_login'] ?? '');
    $password = $_POST['password_login'] ?? '';

    if (!$email) $errors[] = 'Email er påkrævet.';
    if (!$password) $errors[] = 'Adgangskode er påkrævet.';

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $email;
            header('Location: medlem.php'); // Skift til bruger-side
            exit;
        } else {
            $errors[] = 'Forkert email eller adgangskode.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="da">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Log ind | FormueGuiden</title>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f7f9fc;
    color: #153c5d;
    max-width: 420px;
    margin: 3rem auto;
    padding: 2rem;
    border-radius: 10px;
    background-color: #fff;
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
  }
  h1 {
    text-align: center;
    color: #0b1e3d;
    margin-bottom: 1.5rem;
  }
  label {
    display: block;
    margin-bottom: 0.3rem;
    font-weight: 600;
  }
  input[type="email"],
  input[type="password"] {
    width: 100%;
    padding: 0.6rem;
    margin-bottom: 1.2rem;
    border: 1.5px solid #0b1e3d;
    border-radius: 5px;
    font-size: 1rem;
    box-sizing: border-box;
  }
  button {
    width: 100%;
    padding: 0.8rem;
    background-color: #0b1e3d;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 1.1rem;
    transition: background-color 0.3s ease;
  }
  button:hover {
    background-color: #005f8a;
  }
  .message {
    margin-bottom: 1rem;
    padding: 0.7rem 1rem;
    border-radius: 5px;
    box-sizing: border-box;
  }
  .error {
    background-color: #ffe6e6;
    color: #b70000;
    border: 1px solid #b70000;
  }
</style>
</head>
<body>

<h1>Log ind | FormueGuiden</h1>

<?php if ($errors): ?>
  <div class="message error">
    <?php foreach ($errors as $error): ?>
      <div><?php echo htmlspecialchars($error); ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<form method="post" novalidate>
  <label for="email_login">Email</label>
  <input type="email" id="email_login" name="email_login" placeholder="din@email.dk" required />

  <label for="password_login">Adgangskode</label>
  <input type="password" id="password_login" name="password_login" placeholder="Adgangskode" required />

  <button type="submit">Log ind</button>
</form>

</body>
</html>
