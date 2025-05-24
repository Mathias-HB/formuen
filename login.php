<?php
session_start();
require 'dbconfig.php'; // din databasekonfigurationsfil

$errors = [];
$success = '';

// REGISTRERING
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'register') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['passwordConfirm'] ?? '';
    $subscription = $_POST['subscription'] ?? '';
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;

    // Validering
    if (!$email) {
        $errors[] = 'Email er påkrævet.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Indtast en gyldig email.';
    }

    if (!$password) {
        $errors[] = 'Adgangskode er påkrævet.';
    } elseif ($password !== $passwordConfirm) {
        $errors[] = 'Adgangskoderne matcher ikke.';
    }

    if (!$subscription) {
        $errors[] = 'Vælg venligst et abonnement.';
    }

    if (empty($errors)) {
        // Tjek om email findes
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email er allerede registreret.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, subscription, newsletter) VALUES (?, ?, ?, ?)");
            $stmt->execute([$email, $passwordHash, $subscription, $newsletter]);
            $success = 'Din konto er oprettet! Du kan nu logge ind.';
        }
    }
}

// LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $email = trim($_POST['email_login'] ?? '');
    $password = $_POST['password_login'] ?? '';

    if (!$email) {
        $errors[] = 'Email er påkrævet.';
    }
    if (!$password) {
        $errors[] = 'Adgangskode er påkrævet.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $email;
            header('Location: medlem.php');
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
<title>Log ind / Opret bruger | FormueGuiden</title>
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
  .tabs {
    display: flex;
    margin-bottom: 2rem;
    border-bottom: 2px solid #0b1e3d;
  }
  .tab {
    flex: 1;
    text-align: center;
    padding: 1rem;
    font-weight: 700;
    color: #0b1e3d;
    cursor: pointer;
    border-bottom: 4px solid transparent;
    user-select: none;
    transition: border-color 0.3s ease;
  }
  .tab.active {
    border-bottom-color: #0b1e3d;
  }
  form {
    display: none;
  }
  form.active {
    display: block;
  }
  label {
    display: block;
    margin-bottom: 0.3rem;
    font-weight: 600;
  }
  input[type="email"],
  input[type="password"],
  select {
    width: 100%;
    padding: 0.6rem;
    margin-bottom: 1.2rem;
    border: 1.5px solid #0b1e3d;
    border-radius: 5px;
    font-size: 1rem;
    box-sizing: border-box;
  }
  input[type="checkbox"] {
    margin-right: 0.5rem;
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
  .success {
    background-color: #d5f4d5;
    color: #0a7000;
    border: 1px solid #0a7000;
  }
</style>
</head>
<body>

<h1>FormueGuiden - Log ind / Opret bruger</h1>

<div class="tabs">
  <div id="tabLogin" class="tab active" onclick="showForm('login')">Log ind</div>
  <div id="tabRegister" class="tab" onclick="showForm('register')">Opret konto</div>
</div>

<?php if ($errors): ?>
  <div class="message error">
    <?php foreach ($errors as $error): ?>
      <div><?php echo htmlspecialchars($error); ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php if ($success): ?>
  <div class="message success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<form id="loginForm" class="active" method="post" novalidate autocomplete="off">
  <input type="hidden" name="action" value="login" />
  <label for="email_login">Email</label>
  <input type="email" id="email_login" name="email_login" placeholder="din@email.dk" required />

  <label for="password_login">Adgangskode</label>
  <input type="password" id="password_login" name="password_login" placeholder="Adgangskode" required />

  <button type="submit">Log ind</button>
</form>

<form id="registerForm" method="post" novalidate autocomplete="off">
  <input type="hidden" name="action" value="register" />
  <label for="email">Email</label>
  <input type="email" id="email" name="email" placeholder="din@email.dk" required />

  <label for="password">Adgangskode</label>
  <input type="password" id="password" name="password" placeholder="Adgangskode" required />

  <label for="passwordConfirm">Gentag adgangskode</label>
  <input type="password" id="passwordConfirm" name="passwordConfirm" placeholder="Gentag adgangskode" required />

  <label for="subscription">Vælg abonnement</label>
  <select id="subscription" name="subscription" required>
    <option value="">-- Vælg abonnement --</option>
    <option value="basic">Basic</option>
    <option value="premium">Premium</option>
    <option value="pro">Pro</option>
  </select>

  <label><input type="checkbox" id="newsletter" name="newsletter" /> Tilmeld nyhedsbrev</label>

  <button type="submit">Opret konto</button>
</form>

<script>
  function showForm(form) {
    document.getElementById('loginForm').classList.remove('active');
    document.getElementById('registerForm').classList.remove('active');
    document.getElementById('tabLogin').classList.remove('active');
    document.getElementById('tabRegister').classList.remove('active');

    if (form === 'login') {
      document.getElementById('loginForm').classList.add('active');
      document.getElementById('tabLogin').classList.add('active');
    } else {
      document.getElementById('registerForm').classList.add('active');
      document.getElementById('tabRegister').classList.add('active');
    }
  }
</script>

</body>
</html>
