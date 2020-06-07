<?php

session_start();

if(empty($_SESSION) || empty($_GET) || $_SESSION["id"] != $_GET["id"]){ 
/* Certifica-se de que para aceder � p�gina, o utilizador deve estar autenticado, 
   tem de aceder com um id e este id tem de ser o seu pr�prio id */
    
    header("Location: forbidden.php");
    die();

}

$id_user = $_SESSION["id"];

if(!empty($_POST)){ // Certifica-se de que aparece sempre vazio na primeira vez
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $rPassword = $_POST["repeat_pass"];

    $minName = 5;
    $maxName = 18;
    $minPass = 8;
    $maxPass = 48;

    // Lista de erros dos v�rios campos

    $error = array('name' => array(false, 'O nome deve ter entre '. $minName. ' e '. $maxName. ' caracteres e come�ar em letra mai�scula'),
    'email' => array(false, 'Email inv�lido'),
    'password' => array(false, 'A palavra-passe deve ter entre '. $minPass.' e '. $maxPass. ' caracteres'),
    'repeat_pass' => array(false, 'As palavras-passe s�o diferentes'),
    'existing_email' => array(false, 'Email j� existe'));

    require_once("field_validation.php");

    $name = cleanField($name);
    $email = cleanField($email);
    $password = cleanField($password);
    $rPassword = cleanField($rPassword);
    $flag = false; //Serve para ver se existem erros.

    if(!CheckStringAndLength($minName, $maxName, $name)){

        $error["name"][0] = true;
        $flag = true;
        
    }

    if(!checkEmail($email)){

        $error["email"][0] = true;
        $flag = true;

    }

    if(!checkPassword($minPass, $maxPass, $password)){

        $error["password"][0] = true;
        $flag = true;
        
    }

    if($password != $rPassword){

        $error["repeat_pass"][0] = true;
        $flag = true;
    }

    require_once("database.php");

    if(!$flag){

        $queryChange = "SELECT * FROM Users WHERE email = '$email'";

        $resultChange = mysqli_query($myDB, $queryChange);

        if(mysqli_num_rows($resultChange) != 0){

            $error["existing_email"][0] = true;
        }

        else{

            $password = md5($password);
            $query = "UPDATE Users SET nome = '$name', email = '$email', password = '$password' WHERE id_users = '$id_user'";
            $result = mysqli_query($myDB, $query);
            header("Location: profile.php?id=".$id_user."&change=success");
            die();

        }

    }
}

?>



<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Alterar dados</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" media="screen" href="../../front-end/css/main.css" />
        <script src="../../front-end/js/script.js"></script>
    </head>

    <body>

        <button onclick="buttontopScroll()" id="top-button">Topo</button>

        <header>
            <p><i>Produtos regionais portugueses</i></p>
            <img src="../../front-end/img/foto-header.jpg" alt="header" width="100%" height="155">
            <nav>
                <a href="index.php">In�cio</a>
                <a href="products.php">Produtos</a>
                
                <?php

                if(!empty($_SESSION)){

                    $nome = $_SESSION["nome"];
                    
                }


                if(empty($_SESSION)){ // O utilizador � um visitante
                    echo "<a href='register.php'>Registo</a>";
                    echo "<a href='contact.php'>Contacto</a>";
                }

                elseif($_SESSION["tipo_user"] == 3){ // O utilizador � um administrador
                    echo "<a href=profile.php?id=$id_user>Perfil</a>";
                    echo "<a href='indicators.php'>Painel</a>";
                }

                else{ // O utilizador � um comprador/vendedor
                    echo "<a href=profile.php?id=$id_user>Perfil</a>";
                    echo "<a href='contact.php'>Contacto</a>";
                }

                ?>

            </nav>
        </header>

        <main>
            <div class="form-box">
                <p class="form-title">Alterar dados pessoais</p>
                <form action="" method="post" name="form">
                <div class="name-box">
                    Nome: <br>
                    <input type="text" name="name" value="<?php

                        if(!empty($error) && !$error["name"][0]){
                            echo $name;
                        }

                    ?>"> <br>
                    <?php 
                        if(!empty($error) && $error["name"][0]){
                                                         
                        echo $error["name"][1];
                    }
                    ?>
                </div>
                <div class="email-box">
                    Email: <br>
                    <input type="email" name="email" value="<?php
                    
                        if(!empty($error) && !$error["email"][0]){
                            echo $email;
                        }

                    ?>"> <br>
                    <?php 
                        if(!empty($error) && $error["email"][0]){
                                                         
                        echo $error["email"][1];
                        }

                        elseif(!empty($error) && $error["existing_email"][0]){
                            echo $error["existing_email"][1];
                        }
                    ?>
                </div>
                <div class="password-box">
                    Palavra-passe: <br>
                    <input type="password" name="password" value="<?php
                    
                    if(!empty($error) && !$error["password"][0]){
                        echo $password;
                    }

                ?>"> <br>
                <?php 
                    if(!empty($error) && $error["password"][0]){
                                                     
                    echo $error["password"][1];
                }
                ?>
                </div>
                <div class="r_password-box">
                    Repetir palavra-passe: <br>
                    <input type="password" name="repeat_pass" value="<?php
                    
                    if(empty($password)){
                        $rPassword = "";
                    }

                    if(!empty($error) && !$error["repeat_pass"][0]){
                        echo $rPassword;
                    }

                ?>"> <br>
                <?php 

                    if(isset($password) && empty($rPassword)){
                        echo "Repita a palavra-passe inserida";
                    }

                    elseif(!empty($error) && $error["repeat_pass"][0]){
                                                     
                        echo $error["repeat_pass"][1];
                    }

                ?>
                </div>
                <input type="submit" value="Registar">
                </form>
                <br>
                <?php 
                    
                    echo "<a href=profile.php?id=$id_user>Voltar</a>";

                ?>

            </div>
        </main>

        <footer>
            <p>&copy; Produtos regionais de Portugal - Todos os direitos reservados</p>
        </footer>
    </body>
</html>