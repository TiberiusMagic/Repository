<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PhotoBook</title>
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

//print_r($_POST);
//prepare error indicator
$error = "<p class=" . "error_message" . ">";
$error_count = 0;

if(!isset($_POST["last_name"]) || empty(trim($_POST["last_name"])) ||
    !isset($_POST["first_name"]) || empty(trim($_POST["first_name"])) ||
    !isset($_POST["email"]) || empty(trim($_POST["email"]))) {
    $error .= "Kérlek tölts ki minden mezőt!<br>";
    $error_count++;
}
/*if(isset($_POST["email"])){
    if($_POST["email"]!=$_SESSION["email"]){
        //ha megváltozott, megnézzük helyes-e (van-e már ilyen, +formátum) :
        $containsEmail = oci_fetch_array(check_email($_POST['email']), OCI_ASSOC + OCI_RETURN_NULLS);
        if ($containsEmail['CONTAINS'] > 0) {
            $emailError = true;
        }else{
            $sql="UPDATE PB_user SET email=:email WHERE email=:regi";


            $valami=oci_parse($connection,$sql);
            oci_bind_by_name($valami, ':email', $_POST["email"]);
            oci_bind_by_name($valami, ':regi', $_SESSION["email"]);
            oci_execute($valami);
            $_SESSION["email"]=$_POST["email"];
        }
    }
}*/
if(isset($_POST["last_name"])){
    $sql="UPDATE PB_user SET last_name=:uto WHERE email=:email";
    $valami=oci_parse($connection,$sql);
    oci_bind_by_name($valami, ':uto', $_POST["last_name"]);
    oci_bind_by_name($valami, ':email', $_SESSION["email"]);
    oci_execute($valami);
}

if(isset($_POST["first_name"])){
    $sql="UPDATE PB_user SET first_name=:elo WHERE email=:email";
    $valami=oci_parse($connection,$sql);
    oci_bind_by_name($valami, ':elo', $_POST["first_name"]);
    oci_bind_by_name($valami, ':email', $_SESSION["email"]);
    oci_execute($valami);
}

if(isset($_POST["user_category_add"])){
    //$sql="INSERT INTO PB_Follow VALUES (:email,:cati) WHERE NOT EXISTS (SELECT 1 FROM PB_Follow WHERE email = :email AND category = :cati)";
    $sql="INSERT INTO PB_FOLLOW (email, category)
    SELECT :email, :cati
        FROM dual
    WHERE NOT EXISTS (
        SELECT 1 FROM PB_Follow 
        WHERE email = :email AND category = :cati
    )";
    $valami=oci_parse($connection,$sql);
    oci_bind_by_name($valami, ':email', $_SESSION["email"]);
    oci_bind_by_name($valami, ':cati', $_POST["user_category_add"]);
    oci_execute($valami);
}

if(isset($_POST["old_pass"]) && isset($_POST["new_pass"]) && isset($_POST["new_pass_again"])){
    $user = oci_fetch_array(get_password_and_admin($_SESSION['email']), OCI_ASSOC + OCI_RETURN_NULLS);
    if(hash('sha256',$_POST["old_pass"])==$user['PASSWORD']){
        if($_POST["new_pass"]==$_POST["new_pass_again"]){
            $hashelt=hash('sha256',$_POST["new_pass"]);
            $sql="UPDATE PB_user SET password=:pass WHERE email=:email";
            $valami=oci_parse($connection,$sql);
            oci_bind_by_name($valami, ':pass', $hashelt);
            oci_bind_by_name($valami, ':email', $_SESSION["email"]);
            oci_execute($valami);
        }
    }
}

?>

    <div class="container">
        <form action="my_account.php" method="post">
            <div class="balra">
            <h1>
             <?php
                    $tutu=$_SESSION["email"];
                    //$sql = 'SELECT last_name FROM PB_user WHERE $_SESSION["email"] = email';
                    if (!($connection = connect())){
                        return false;
                    }

                    $faka = oci_parse($connection, "SELECT last_name, first_name FROM PB_user WHERE email='".$_SESSION['email']."'");

                    /*if (!$faka) {
                        $error = oci_error($connection);
                        oci_close($connection);
                        echo $error['message'] . "\n";
                        die();
                    }*/

                    oci_execute($faka);
                    if (!$faka) {
                        $error = oci_error($faka);
                        oci_close($connection);
                        echo $error['message'] . "\n";
                        die();
                    }

                    while($row=oci_fetch_array($faka)){
                        echo 'Hello, '.$row[0]." ".$row[1];


                    }

                    oci_close($connection);
                    ?>
            </h1>
            <br>
            <label>Vezetéknév</label><br>
            <input type="text" name="last_name" value="<?php
            $tutu=$_SESSION["email"];
            //$sql = 'SELECT last_name FROM PB_user WHERE $_SESSION["email"] = email';
            if (!($connection = connect())){
                return false;
            }

            $faka = oci_parse($connection, "SELECT last_name, first_name FROM PB_user WHERE email=:email");

            /*if (!$faka) {
                $error = oci_error($connection);
                oci_close($connection);
                echo $error['message'] . "\n";
                die();
            }*/
            oci_bind_by_name($faka, ':email', $_SESSION["email"]);
            oci_execute($faka);
            if (!$faka) {
                $error = oci_error($faka);
                oci_close($connection);
                echo $error['message'] . "\n";
                die();
            }

            while($row=oci_fetch_array($faka)){
                echo $row[0];
            }

            oci_close($connection);
            ?>"><br>
            <label>Keresztnév</label><br>
            <input type="text" name="first_name" value="<?php
            $tutu=$_SESSION["email"];
            //$sql = 'SELECT last_name FROM PB_user WHERE $_SESSION["email"] = email';
            if (!($connection = connect())){
                return false;
            }

            $faka = oci_parse($connection, "SELECT last_name, first_name FROM PB_user WHERE email=:email");

            /*if (!$faka) {
                $error = oci_error($connection);
                oci_close($connection);
                echo $error['message'] . "\n";
                die();
            }*/
            oci_bind_by_name($faka, ':email', $_SESSION["email"]);
            oci_execute($faka);
            if (!$faka) {
                $error = oci_error($faka);
                oci_close($connection);
                echo $error['message'] . "\n";
                die();
            }

            while($row=oci_fetch_array($faka)){
                echo $row[1];


            }

            oci_close($connection);
            ?>"><br>
            <label>Email</label><br>
            <input type="text" name="email" disabled value="<?php
            $tutu=$_SESSION["email"];
            //$sql = 'SELECT last_name FROM PB_user WHERE $_SESSION["email"] = email';
            if (!($connection = connect())){
                return false;
            }

            $faka = oci_parse($connection, "SELECT email FROM PB_User WHERE email=:email");

            /*if (!$faka) {
                $error = oci_error($connection);
                oci_close($connection);
                echo $error['message'] . "\n";
                die();
            }*/
            oci_bind_by_name($faka, ':email', $_SESSION["email"]);
            oci_execute($faka);
            if (!$faka) {
                $error = oci_error($faka);
                oci_close($connection);
                echo $error['message'] . "\n";
                die();
            }

            while($row=oci_fetch_array($faka)){
                echo $row[0];


            }

            oci_close($connection);
            ?>"><br>
            <label>Régi jelszó</label><br>
            <input type="password" name="old_pass"><br>
            <label>Új jelszó</label><br>
            <input type="password" name="new_pass"><br>
            <label>Új jelszó megerősítése</label><br>
            <input type="password" name="new_pass_again"><br><br>
            </div>

            <div class="jobbra">
                <h1>Követett kategóriák:</h1>
                <?php
                if (!($connection = connect())){
                return false;
                }
                $existing_cats=array();
                $stid=oci_parse($connection, "SELECT name From PB_Follow, PB_Category WHERE email='".$_SESSION["email"]."' and id=category");
                oci_execute($stid);
                while($row=oci_fetch_array($stid)){
                    echo $row[0];
                    $existing_cats[] = $row['NAME'];
                    //echo '  <input type="checkbox" checked>';
                    echo '<br>';
                }
                ?>

                <select class="form-select" name="user_category_add">
                    <option selected disabled>Válassz a listából</option>
                    <?php
                    $all_cats = list_categories();
                    while ($row = oci_fetch_array($all_cats, OCI_ASSOC + OCI_RETURN_NULLS)) {
                        if (!in_array($row['NAME'],$existing_cats)) {
                            echo '<option value="' . $row['ID'] . '">' . $row['NAME'] . '</option>';
                        }
                    }
                    ?>
                </select>
                <br>
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