<?php
session_start();
include_once('db_connection.php');

if (!($connection = connect())){
    return false;
}

$sql='DELETE FROM user WHERE email=:email';
$stid = oci_parse($connection, $sql);

if (!$stid) {
    $error = oci_error($connection);
    oci_close($connection);
    echo $error['message'] . "\n";
    die();
}

oci_bind_by_name($stid, ':email', $_SESSION["email"]);
oci_execute($stid);
oci_close($connection);
session_destroy();
header("Location: logout.php");
?>
