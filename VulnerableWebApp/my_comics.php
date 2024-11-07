<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Képregényeim - KépregényMánia</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
</head>
<body>
<?php
include_once('header.php');
include_once('db_connection.php');
include_once('pictures_listing.php');

$allowed_extensions = ["jpg", "jpeg", "png"];

if(!session_id()){
    session_start();
}

if (!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) {
    header("Location: index.php");
}

$error = array(
    'fileExists' => false,
    'uploadError' => false,
    'invalidExtension' => false,
);

// picture upload
if (isset($_POST['titleInput']) && isset($_POST['city']) && isset($_POST['category']) &&
    isset($_POST['camera'])) {
    if (isset($_FILES['picture']) && $_FILES['picture']["size"] !== 0) {
        $extension = strtolower(pathinfo($_FILES['picture']["name"], PATHINFO_EXTENSION));

        if (in_array($extension, $allowed_extensions)) {
            if ($_FILES['picture']["error"] === 0) {
                $destination = 'Pictures/'.$_FILES['picture']["name"];

                if (file_exists($destination)) {
                    $error['fileExists'] = true;
                }else{
                    if (move_uploaded_file($_FILES['picture']["tmp_name"], $destination)) {
                        echo "Sikeres fájlfeltöltés! <br/>";
                        add_picture($_FILES['picture']["name"], $_POST['titleInput'], $_SESSION['email'],
                                    $_POST['camera'], $_POST['city']);
                        add_picture_to_category($_FILES['picture']["name"], $_POST['category']);
                    } else {
                        $error['uploadError'] = true;
                    }
                }
            } else {
                $error['uploadError'] = true;
            }
        } else {
            $error['invalidExtension'] = true;
        }
    }
}
?>

<!--picture upload form-->
<div class="card text-bg-light login-register-card">
    <div class="login-register-form">
        <form method="post" action="my_comics.php" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="titleInput" class="form-label">Cím</label>
                <input type="text" name="titleInput" class="form-control" id="titleInput" required>
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
                <label for="category">Kategória</label>
                <select class="form-select" required name="category" id="category">
                <option selected disabled value="">Válassz a listából</option>
                <?php
                $stid = list_categories();
                while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
                    echo '<option value="'.$row['ID'].'">'.$row['NAME'].'</option>';
                }
                ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="camera">Fényképező</label>
                <select class="form-select" required name="camera" id="camera">
                <option selected disabled value="">Válassz a listából</option>
                <?php
                $stid = list_cameras_with_makers();
                while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
                    echo '<option value="'.$row['ID'].'">'.$row['MAKER'].' '.$row['NAME'].'</option>';
                }
                ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="formFile" class="form-label">Kép kiválasztása</label>
                <input class="form-control" type="file" id="formFile" name="picture" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary" style="margin-bottom: 10px">Kép feltöltése</button>
        </form>
            <?php
            if ($error['fileExists']) {
                echo '<div class="alert alert-danger" style="margin-top: 5px" role="alert">Ilyen nevű fájl már létezik!</div>';
            }

            if ($error['uploadError']) {
                echo '<div class="alert alert-danger" style="margin-top: 5px" role="alert">A fájlfeltöltés nem sikerült!</div>';
            }

            if ($error['fileExists']) {
                echo '<div class="alert alert-danger" style="margin-top: 5px" role="alert">A fájl kiterjesztése nem megfelelő!</div>';
            }
            ?>
    </div>
</div>

<?php
// display user pictures
user_pictures_listing($_SESSION['email']);
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>