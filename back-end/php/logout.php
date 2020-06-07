<?php

session_start();

session_destroy(); // Apaga todos os dados da sessão

header("Location: login.php");

?>