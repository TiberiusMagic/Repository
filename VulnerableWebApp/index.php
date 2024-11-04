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

// list 3 of the most popular foreign cities
echo '<div class="card" style="margin: 10px">
            <div class="card-body">';
echo    '<h5 class="card-title">Legnépszerűbb út célok</h5>';
echo    '<div style="align-content: center; text-align: center">';
$cities = list_popular_foreign_cities();
while ($row = oci_fetch_array($cities, OCI_ASSOC + OCI_RETURN_NULLS)) {
    echo '<span style="padding-left: 5%; padding-right: 5%; text-align: center">'.$row['NAME'].'</span>';
}
echo '</div>
    </div>
</div>';

// display pictures based on context
if (isset($_GET['category'])) {
    category_listing($_GET['category']);
} elseif (!empty($_GET['search'])) {
    search_listing($_GET['search']);
} else{
    homepage_listing();
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>