<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PhotoBook</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
</head>
<body>
<?php
include_once('header.php');
include_once('db_connection.php');
include_once('pictures_listing.php');

if(!session_id()){
    session_start();
}

if (isset($_POST['contest']) && isset($_SESSION['Admin']) && $_SESSION['Admin']) {
    $stid = close_contest($_POST['contest']);
}

?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
<?php
$contests = list_contests();
while ($row = oci_fetch_array($contests, OCI_ASSOC + OCI_RETURN_NULLS)) {
    echo '<div class="card" style="margin: 10px">
           <div class="card-body">';
    echo    '<h5 class="card-title">'.$row['TITLE'].'</h5>';
    echo    '<p class="card-text">'.$row['DESCRIPTION'].'</p>';
    echo    '<p class="card-text">Határidő: '.$row['END_DATE'].'</p>';
    if (!$row['CLOSED'] && isset($_SESSION['Admin']) && $_SESSION['Admin']) {
        echo '<form method="post" action="contests.php">
                <input type="text" name="contest" hidden="hidden" value="'.$row['ID'].'">
                <button type="submit" class="btn btn-primary" style="margin: 5px" name="close_button">Lezárás</button>
              </form>';
    }
    contest_listing($row['ID'], $row['CLOSED']);
    echo '
    </div>
</div>';
}
?>
</html>