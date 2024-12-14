<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KépregényMánia</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
</head>
<body>
<?php
include_once('header.php');
include_once('db_connection.php');

if(!session_id()){
    session_start();
}

if(isset($_SESSION['loggedIn'])){ //Csak akkor látjuk a képregényeket, ha be vagyunk jelentkezve
    if ($_SESSION['loggedIn']) {
        if (!($connection = connect())) {
            die("Hiba az adatbázis-kapcsolat létrehozásakor!");
        }

        try {
            // SQL lekérdezés az összes képregény adatának lekéréséhez
            $sql = "SELECT id, title, price FROM comic";
            $stmt = $connection->prepare($sql);
            $stmt->execute();

            // Képregények listájának kiírása
            echo '<div class="comic-list">';
            echo '<br>';
            while ($comic = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $title = $comic['title'];
                $price = $comic['price'];
                $imagePath = "kepek/" . $title . ".jpg"; // A borítók mappa helye

                echo '<div class="comic-item">';
                echo '<h3>' . htmlspecialchars($title) . '</h3>';
                echo '<p>Ár: ' . htmlspecialchars($price) . ' Ft</p>';

                // Ellenőrizni, hogy létezik-e a borító kép
                if (file_exists($imagePath)) {
                    ?>

                    <a href="comic.php?id=<?php echo $comic['id']; ?>">
                        <img src="kepek/<?php echo htmlspecialchars($comic['title']); ?>.jpg" alt="<?php echo htmlspecialchars($comic['title']); ?>">
                    </a>


                <?php } else {
                    echo '<p>Nincs elérhető borító.</p>';
                }

                echo '</div>';
            }
            echo '</div>';

        } catch (PDOException $e) {
            die("Hiba történt az adatok lekérésekor: " . $e->getMessage());
        }
    }else{
        echo '<h1>Üdvözöllek a KépregényMánia oldalán!</h1>';
        echo '<h2>A tartalmakért jelentkezz be, vagy regisztrálj a fenti Saját fiókra nyomva</h2>';
    }

}else{
echo '<h1>Üdvözöllek a KépregényMánia oldalán!</h1>';
echo '<h2>A tartalmakért jelentkezz be, vagy regisztrálj a fenti Saját fiókra nyomva</h2>';
}


?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>