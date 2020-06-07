<?php

session_start();

if(empty($_SESSION) || empty($_GET) || $_SESSION["tipo_user"] != 3){
    header("Location: forbidden.php");
}

else{
    $id_user = $_GET["user"];

    require_once("database.php");

    $queryPriv = "UPDATE Users SET tipo_user = 3 WHERE id_users = '$id_user'";

    $resultPriv = mysqli_query($myDB, $queryPriv);

    if(!$resultPriv){
        header("Location: error_database.php");
        die();
    }

    else{
        header("Location: indicators.php?set=admin");
        die();
    }
}

?>