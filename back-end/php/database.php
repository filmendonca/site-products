<?php

// Ficheiro para conectar � base de dados

$hostname = "localhost";
$username = "alexandra";
$userpass = "cm";
$database = "comercio_database";
$myDB = mysqli_connect($hostname, $username, $userpass, $database);

if(!$myDB){
    echo "Erro na conex�o � base de dados. Tente novamente mais tarde.";
    die();
}

?>