<?php
session_start();
include_once('db_connection.php');

if (!($connection = connect())){
    return false;
}

print_r($_POST);
//prepare error indicator
$error = "<p class=" . "error_message" . ">";
$error_count = 0;

if(!isset($_POST["last_name"]) || empty(trim($_POST["last_name"])) ||
    !isset($_POST["first_name"]) || empty(trim($_POST["first_name"])) ||
    !isset($_POST["email"]) || empty(trim($_POST["email"]))) {
    $error .= "Kérlek tölts ki minden mezőt!<br>";
    $error_count++;
}

if($_POST["email"]!=$_SESSION["email"]){
    //ha megváltozott, megnézzük helyes-e (van-e már ilyen, +formátum) :
    $containsEmail = oci_fetch_array(check_email($_POST['email']), OCI_ASSOC + OCI_RETURN_NULLS);

}



?>
