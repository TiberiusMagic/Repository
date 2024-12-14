<?php
session_start();
include_once('db_connection.php');

if (!($connection = connect())){
    return false;
}

try {
    $sql = 'DELETE FROM user WHERE email = :email';
    $stmt = $connection->prepare($sql);

    $stmt->bindParam(':email', $_SESSION["email"]);

    $stmt->execute();

    session_destroy();
    header("Location: logout.php");
    exit();
} catch (PDOException $e) {
    die("Hiba történt a felhasználó törlésekor: " . $e->getMessage());
}

