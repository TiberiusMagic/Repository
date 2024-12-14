<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bejelentkezés - KépregényMánia</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>
<body>
<?php

include_once('db_connection.php');

if(!session_id()){
    session_start();
}

$error = false;

if (isset($_POST['register_button'])) {
    header("Location: register.php");
}

if (isset($_POST['emailInput']) && isset($_POST['passwordInput'])) {
    if (!empty($_POST['emailInput']) && !empty($_POST['passwordInput'])) {
        $user = get_password_and_admin($_POST['emailInput']);
        if ($user['password'] == hash('sha256', $_POST['passwordInput'])) { //TODO: hash algoritmusokat nézni (Elavultak vs Validak)
            $_SESSION['loggedIn'] = true;
            $_SESSION['email'] = $_POST['emailInput'];
            if ($user['admin'] == 1) {
                $_SESSION['Admin'] = true;
            } else {
                $_SESSION['Admin'] = false;
            }
            header("Location: index.php");
            exit(); // Fontos!
        } else {
            $error = true;
        }
    }
}
include_once('header.php');
?>
    <div class="card text-bg-light login-register-card">
        <span class="login-register-form">
            <form method="post" action="login.php">
            <div class="mb-3">
                <label for="emailInput" class="form-label">Email cím</label>
                <input type="email" name="emailInput" class="form-control" id="emailInput" required>
            </div>
            <div class="mb-3">
                <label for="passwordInput" class="form-label">Jelszó</label>
                <input type="password" name="passwordInput" class="form-control" id="passwordInput" required>
                <div class="invalid-feedback">
                    Please choose a username.
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin: 5px">Belépés</button>
        </form>

        <form method="post" action="login.php">
            <button type="submit" class="btn btn-primary" style="margin: 5px" name="register_button">Regisztráció</button>
        </form>
            <?php
            if ($error) {
                echo '<div class="alert alert-danger" role="alert">Hibás felhasználónév vagy jelszó</div>';
            }
            ?>
        </span>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>
