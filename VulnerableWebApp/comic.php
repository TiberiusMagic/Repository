<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KépregényMánia</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>
<body>
<?php
include_once('header.php');
include_once('db_connection.php');

if (!session_id()) {
    session_start();
}
if (!($connection = connect())) {
    die("Nem sikerült csatlakozni az adatbázishoz.");
}

if (!isset($_GET['id'])) {
    die("Hiba: Nincs megadva képregény azonosító!");
}
$id = $_GET['id'];

$sql = "SELECT title, price, owner FROM comic WHERE id = :id";
$stmt = $connection->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$comic = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$comic) {
    die("Nem található a képregény az adatbázisban.");
}

$commentSql = "SELECT user_email, content FROM comment WHERE comic_id = :id ORDER BY id ASC";
$commentStmt = $connection->prepare($commentSql);
$commentStmt->bindParam(':id', $id, PDO::PARAM_INT);
$commentStmt->execute();
$comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="comic-detail">
        <h1><?php echo htmlspecialchars($comic['title']); ?></h1>
        <img src="kepek/<?php echo htmlspecialchars($comic['title']); ?>.jpg" alt="<?php echo htmlspecialchars($comic['title']); ?>">
        <p>Ár: <?php echo htmlspecialchars($comic['price']); ?> Ft</p>
        <p>Tulajdonos: <?php echo htmlspecialchars($comic['owner']); ?></p>
        <?php if ($comic['owner'] != $_SESSION['email']) { //Ha a miénk, nem tudjuk megvásárolni?>
            <form action="purchase_comic.php" method="post">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <button type="submit">Megvásárlom</button><br><br>
            </form>
        <?php } ?>

        <hr>

        <h2>Kommentek:</h2>
        <?php if ($comments): ?>
            <ul class="list-group">
                <?php foreach ($comments as $comment): ?>
                    <li class="list-group-item">
                        <strong><?php echo htmlspecialchars($comment['user_email']); ?>:</strong>
                        <p><?php echo htmlspecialchars($comment['content']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Nincsenek kommentek ehhez a képregényhez. Te lehetsz az első!</p>
        <?php endif; ?>

        <?php if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) { ?>
            <h3>Szólj hozzá!</h3>
            <form action="add_comment.php" method="post">
                <input type="hidden" name="comic_id" value="<?php echo $id; ?>">
                <div class="mb-3">
                    <textarea class="form-control" name="comment_content" rows="3" placeholder="Írj egy kommentet..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Hozzászólás</button>
            </form>
        <?php } else { ?>
            <p><a href="login.php">Jelentkezz be</a>, hogy kommentelhess!</p>
        <?php } ?>

    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
            crossorigin="anonymous"></script>
</body>
</html>