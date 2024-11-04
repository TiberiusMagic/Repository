<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registráció - PhotoBook</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>
<body>
<?php
include_once('header.php');
include_once('db_connection.php');

if(!session_id()){
    session_start();
}

$emailError = false;
$passwordError = false;

if (isset($_POST['login_button'])) {
    header("Location: login.php");
}

if (isset($_POST['lastNameInput']) && isset($_POST['firstNameInput']) && isset($_POST['city']) &&
    isset($_POST['emailInput']) && isset($_POST['passwordInput']) && isset($_POST['repasswordInput'])) {
    $containsEmail = oci_fetch_array(check_email($_POST['emailInput']), OCI_ASSOC + OCI_RETURN_NULLS);

    if ($containsEmail['CONTAINS'] > 0) {
        $emailError = true;
    } else if ($_POST['passwordInput'] != $_POST['repasswordInput']) {
        $passwordError = true;
    } else {
        $hashedPass = hash('sha256', $_POST['passwordInput']);
        $result = register_user($_POST['emailInput'], $hashedPass, $_POST['lastNameInput'],
        $_POST['firstNameInput'], $_POST['city']);

        if ($result) {
            header("Location: login.php");
        }
    }
}

?>
<div class="card text-bg-light login-register-card">
    <span class="login-register-form">
        <form method="post" action="register.php">
            <div class="mb-3">
            <label for="lastNameInput" class="form-label">Vezetéknév</label>
            <input type="text" required name="lastNameInput" class="form-control" id="lastNameInput">
        </div>
        <div class="mb-3">
            <label for="firstNameInput" class="form-label">Keresztnév</label>
            <input type="text" required name="firstNameInput" class="form-control" id="firstNameInput">
        </div>
        <div class="mb-3">
            <label for="city">Város</label>
            <select class="form-select" required name="city" id="city">
                <option selected disabled value="">Válassz a listából</option>
                <?php
                $stid = list_cities();
                while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
                    echo '<option value="'.$row['ID'].'">'.$row['NAME'].'</option>';
                }
                ?>
            </select>
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
