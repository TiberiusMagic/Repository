<?php
session_start();
include_once('db_connection.php');

if (!($connection = connect())) {
    die("Nem sikerült csatlakozni az adatbázishoz.");
}

if (!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) {
    die("Be kell jelentkezni, hogy kommentelhess.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comic_id = $_POST['comic_id'];
    $user_email = $_SESSION['email'];
    $content = trim($_POST['comment_content']);

    if (empty($content)) {
        die("A komment nem lehet üres.");
    }

    $sql = "INSERT INTO comment (comic_id, user_email, content) VALUES (:comic_id, :user_email, :content)";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':comic_id', $comic_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_email', $user_email, PDO::PARAM_STR);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);

    if ($stmt->execute()) {
        header("Location: comic.php?id=" . $comic_id);
        exit;
    } else {
        die("Nem sikerült hozzáadni a kommentet.");
    }
} else {
    die("Helytelen kérés.");
}
?>
