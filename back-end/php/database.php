<?php

// Ficheiro para conectar à base de dados

$hostname = "localhost";
$username = "alexandra";
$userpass = "cm";
$database = "comercio_database";
$myDB = mysqli_connect($hostname, $username, $userpass, $database);

if(!$myDB){
    echo "Erro na conexão à base de dados. Tente novamente mais tarde.";
    die();
}

?>