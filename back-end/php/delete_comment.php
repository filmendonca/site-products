<?php

session_start();

if(empty($_SESSION) || empty($_GET) || $_SESSION["id_produto"] != $_GET["id"]){
/* Certifica-se de que para aceder à página, o utilizador deve estar autenticado, 
   tem de aceder com um id e este id tem de ser igual ao id do produto da página onde se encontrava */    

    header("Location: forbidden.php");
    die();

}

$id_produto = $_GET["id"];
$id_user = $_SESSION["id"];

require_once("database.php");

$queryDelete = "DELETE FROM Comentarios WHERE id_produto = '$id_produto' AND id_users = '$id_user'";

$resultDelete = mysqli_query($myDB, $queryDelete);

if(!$resultDelete){
    header("Location: error_database.php");
    die();
}

else{
    $_GET["comment"] = "deleted"; // Indica que o comentário foi apagado
    header("Location: view_product.php?id=".$id_produto."&comment=".$_GET["comment"]);
    die();
}


?>