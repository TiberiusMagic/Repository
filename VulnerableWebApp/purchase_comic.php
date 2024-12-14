<?php
session_start();
include_once('db_connection.php');

if (!($connection = connect())){
    return false;
}

try {
    $money = money_of_user($_SESSION['email']);
    $sql = "SELECT price FROM comic WHERE id=:id";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':id', $_POST['id']);

    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if($money['money_forint'] >= $result['price']){
        $sql = "UPDATE comic SET owner=:email WHERE id=:id";
        $stmt = $connection->prepare($sql);

        $stmt->bindParam(':email', $_SESSION["email"]);
        $stmt->bindParam(':id', $_POST["id"]);

        $stmt->execute();

        $sql = "UPDATE user SET money_forint=money_forint-:price WHERE email=:email";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':email', $_SESSION["email"]);
        $stmt->bindParam(':price', $result['price']);
        $stmt->execute();
    }else{
        echo 'Nincs elég pénzed erre. Tölthetsz fel egyenleget, ha menüsávban rányomsz a pénzedre.';
    }

    header("Location: index.php");
} catch (PDOException $e) {
    die("Hiba történt a vásárlásnál: " . $e->getMessage());
}

?>
