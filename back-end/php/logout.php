<?php

session_start();

session_destroy(); // Apaga todos os dados da sess�o

header("Location: login.php");

?>