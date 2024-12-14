<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KépregényMánia</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>
<body>
<?php
include_once('header.php');
include_once('db_connection.php');
if (!session_id()) {
    session_start();
}
if (!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) {
    header("Location: index.php");
}

if (!($connection = connect())){
    return false;
}
//Kiíratni a bejelentkezett felhasználó összes kártyájának hashelt adatait:
/*$sql = "SELECT * FROM bank_card WHERE user_email=:email";
$stmt = $connection->query($sql);

$stmt->bindParam(':email', $_SESSION["email"]);

$stmt->execute();

echo "<br>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "Kártyaszám: " . $row['card_number'] . ", Kártyára írt Név: " . $row['card_name'] .
        ", Lejárati dátum: " . $row['expire_date'] . ", CVC kód: " . $row['cvc_code'] . "<br>";
}*/
?>
<div class="container">
    <h2>Egyenleg feltöltése</h2>
    <form action="bank_card_topup_process.php" method="post">
        <div>
            <label>Kártyaszám:</label>
            <input type="text" name="card_number" required>
        </div>
        <div>
            <label>Kártyára írt név:</label>
            <input type="text" name="cardholder_name" required>
        </div>
        <div>
            <label>Lejárati dátum:</label>
            <input type="text" name="expiry_date" placeholder="MM/YY" required>
        </div>
        <div>
            <label>CVC kód:</label>
            <input type="text" name="cvc" required>
        </div>
        <div>
            <label>Feltöltendő összeg (HUF):</label>
            <input type="number" name="amount" required>
        </div>
        <div>
            <button type="submit">Feltöltés</button>
        </div>
    </form>
</div>
</body>
</html>