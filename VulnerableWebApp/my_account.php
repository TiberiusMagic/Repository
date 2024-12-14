<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KépregényMánia</title>
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

if (!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) {
    header("Location: index.php");
}

if (!($connection = connect())){
    return false;
}

$emailError = false;
$passwordError = false;

$error = "<p class=" . "error_message" . ">";
$error_count = 0;

if(!isset($_POST["name"]) || empty(trim($_POST["name"])) ||
    !isset($_POST["email"]) || empty(trim($_POST["email"]))) {
    $error .= "Kérlek tölts ki minden mezőt!<br>";
    $error_count++;
}

if(isset($_POST["name"])){
    $sql="UPDATE user SET name=:name WHERE email=:email";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':name', $_POST["name"], PDO::PARAM_STR);
    $stmt->bindParam(':email', $_SESSION["email"], PDO::PARAM_STR);

    try {
        $stmt->execute();
        echo "<p class='success_message'>Név sikeresen módosítva.</p>";
    } catch (PDOException $e) {
        echo "<p class='error_message'>Hiba történt a név módosítása során: " . $e->getMessage() . "</p>";
    }
}

if(isset($_POST["old_pass"]) && isset($_POST["new_pass"]) && isset($_POST["new_pass_again"])){
    $user = get_password_and_admin($_SESSION['email']);
    if(hash('sha256',$_POST["old_pass"])==$user['password']){
        if($_POST["new_pass"]==$_POST["new_pass_again"]){
            $hashelt=hash('sha256',$_POST["new_pass"]);
            $sql="UPDATE user SET password=:pass WHERE email=:email";

            $stmt = $connection->prepare($sql);
            $stmt->bindParam(':pass', $hashelt, PDO::PARAM_STR);
            $stmt->bindParam(':email', $_SESSION["email"], PDO::PARAM_STR);

            try {
                $stmt->execute();
                echo "<p class='success_message'>Jelszó sikeresen módosítva.</p>";
            } catch (PDOException $e) {
                echo "<p class='error_message'>Hiba történt a jelszó módosítása során: " . $e->getMessage() . "</p>";
            }
        }else {
            echo "<p class='error_message'>Az új jelszavak nem egyeznek.</p>";
        }
    }
}

?>

    <div class="container">
        <form action="my_account.php" method="post">
            <div class="balra">
            <h1>
             <?php
                    if (!($connection = connect())){
                        return false;
                    }
             $sql = "SELECT name FROM user WHERE email = :email";
             $stmt = $connection->prepare($sql);
             $stmt->bindParam(':email', $_SESSION['email']);
             $stmt->execute();

             $user = $stmt->fetch(PDO::FETCH_ASSOC);
             if ($user) {
                 echo 'Hello, ' . htmlspecialchars($user['name']);
             }
                    ?>
            </h1>
            <br>
            <label>Név</label><br>
            <input type="text" name="name" value="<?php
            if ($user) {
                echo htmlspecialchars($user['name']);
            }
            ?>"><br>
            <br>
            <label>Email</label><br>
            <input type="text" name="email" disabled value="<?php
            echo htmlspecialchars($_SESSION['email']);
            ?>"><br>
            <label>Régi jelszó</label><br>
            <input type="password" name="old_pass"><br>
            <label>Új jelszó</label><br>
            <input type="password" name="new_pass"><br>
            <label>Új jelszó megerősítése</label><br>
            <input type="password" name="new_pass_again"><br><br>
            </div>

            <input type="submit" value="Módosítás" id="modify"><br><br>
        </form>
        <form action="acc_delete_process.php" method="post">
            <input type="submit" value="Fiók törlése" id="delete"><br><br>
        </form>
        <?php
        if ($emailError) {
            echo '<div class="alert alert-danger" role="alert">Már létezik fiók a megadott email címmel</div>';
        } else if ($passwordError) {
            echo '<div class="alert alert-danger" role="alert">A jelszavak nem egyeznek</div>';
        }
        ?>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>