<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Regisztráció - KépregényMánia</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>
<body>
<?php

include_once('db_connection.php');

if(!session_id()){
    session_start();
}

$emailError = false;
$passwordError = false;

if (isset($_POST['login_button'])) {
    header("Location: login.php");
}

if (isset($_POST['nameInput']) && isset($_POST['emailInput']) &&
    isset($_POST['passwordInput']) && isset($_POST['repasswordInput'])) {

    if (check_email($_POST['emailInput'])) {
        $emailError = true;
    } else if ($_POST['passwordInput'] != $_POST['repasswordInput']) {
        $passwordError = true;
    } else {
        $hashedPass = hash('sha256', $_POST['passwordInput']);
        $result = register_user($_POST['emailInput'], $hashedPass, $_POST['nameInput']);

        if ($result) {
            header("Location: login.php");
        }
    }
}
include_once('header.php');
?>
<div class="card text-bg-light login-register-card">
    <span class="login-register-form">
        <form method="post" action="register.php">
            <div class="mb-3">
            <label for="nameInput" class="form-label">Név</label>
            <input type="text" required name="nameInput" class="form-control" id="nameInput">
        </div>

        <div class="mb-3">
            <label for="emailInput" class="form-label">Email cím</label>
            <input type="email" required name="emailInput" class="form-control" id="emailInput">
        </div>
        <div class="mb-3">
            <label for="passwordInput" class="form-label">Jelszó</label>
            <input type="password" required name="passwordInput" class="form-control" id="passwordInput">
        </div>
        <div class="mb-3">
            <label for="repasswordInput" class="form-label">Jelszó megerősítése</label>
            <input type="password" required name="repasswordInput" class="form-control" id="repasswordInput">
        </div>
        <button type="submit" class="btn btn-primary" style="margin: 5px">Regisztráció</button>
    </form>

        <form method="post" action="login.php">
            <button type="submit" class="btn btn-primary" style="margin: 5px" name="login_button">Bejelentkezés</button>
        </form>
        <?php
        if ($emailError) {
            echo '<div class="alert alert-danger" role="alert">Már létezik fiók a megadott email címmel</div>';
        } else if ($passwordError) {
            echo '<div class="alert alert-danger" role="alert">A jelszavak nem egyeznek</div>';
        }
        ?>
    </span>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>
