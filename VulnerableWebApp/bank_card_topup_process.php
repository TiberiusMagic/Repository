<?php
include_once('db_connection.php');
if (!session_id()) {
    session_start();
}
if (!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) {
    header("Location: index.php");
}

$connection = connect();
$user_email = $_SESSION['email'];

$card_number = $_POST['card_number'];
$cardholder_name = $_POST['cardholder_name'];
$expiry_date = $_POST['expiry_date'];
$cvc = $_POST['cvc'];
$amount = (int)$_POST['amount'];

if (!$amount || $amount <= 0) {
    die("Érvénytelen összeg!");
}

// Hash-elés
$card_number_hash = hash('sha256', $card_number);
$cardholder_name_hash = hash('sha256', $cardholder_name);
$expiry_date_hash = hash('sha256', $expiry_date);
$cvc_hash = hash('sha256', $cvc);

try {
    $connection->beginTransaction();

    // Ellenőrizzük, hogy a kártya létezik-e
    $stmt = $connection->prepare(
        "SELECT id FROM bank_card 
         WHERE card_number = :card_number_hash 
         AND card_name = :cardholder_name_hash 
         AND expire_date = :expiry_date_hash 
         AND cvc_code = :cvc_hash 
         AND user_email = :user_id"
    );
    $stmt->bindParam(':card_number_hash', $card_number_hash);
    $stmt->bindParam(':cardholder_name_hash', $cardholder_name_hash);
    $stmt->bindParam(':expiry_date_hash', $expiry_date_hash);
    $stmt->bindParam(':cvc_hash', $cvc_hash);
    $stmt->bindParam(':user_id', $user_email);
    $stmt->execute();

    if ($stmt->fetch()) {
        // Ha létezik a kártya, frissítjük az egyenleget
        $stmt = $connection->prepare("UPDATE user SET money_forint = money_forint + :amount WHERE email = :user_email");
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':user_email', $user_email);
        $stmt->execute();
    } else {
        // Ha nem létezik a kártya, hozzáadjuk
        $stmt = $connection->prepare(
            "INSERT INTO bank_card (card_number, card_name, expire_date, cvc_code, user_email) 
             VALUES (:card_number_hash, :cardholder_name_hash, :expiry_date_hash, :cvc_hash, :user_email)"
        );
        $stmt->bindParam(':card_number_hash', $card_number_hash);
        $stmt->bindParam(':cardholder_name_hash', $cardholder_name_hash);
        $stmt->bindParam(':expiry_date_hash', $expiry_date_hash);
        $stmt->bindParam(':cvc_hash', $cvc_hash);
        $stmt->bindParam(':user_email', $user_email);
        $stmt->execute();

        // Frissítjük az egyenleget
        $stmt = $connection->prepare("UPDATE user SET money_forint = money_forint + :amount WHERE email = :user_email");
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':user_email', $user_email);
        $stmt->execute();
    }

    $connection->commit();
    header("Location: index.php");
} catch (Exception $e) {
    $connection->rollBack();
    die("Hiba történt: " . $e->getMessage());
}
?>