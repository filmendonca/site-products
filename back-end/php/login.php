<?php
    
    session_start();

    if(!empty($_SESSION)){
        header("Location: index.php");
        die();
    }

    if(!empty($_POST)){
        $email = $_POST["email"];
        $password = $_POST["password"];

        $minPass = 8;
        $maxPass = 48;

        $error = array('email' => array(false, 'Email inválido'),
        'password' => array(false, 'A password deve ter entre '. $minPass.' e '. $maxPass. ' caracteres'),
        'auth' => array(false, 'Email ou password incorretos')); // Autenticação

        // Não especifica ao utilizador que o email ou a password existam caso estes estejam errados

        require_once("field_validation.php");
        
        $email = cleanField($email);
        $password = cleanField($password);
        $flag = false; //Serve para ver se existem erros.

        if(!checkEmail($email)){

            $error["email"][0] = true;
            $flag = true;

        }


        if(!checkPassword($minPass, $maxPass, $password)){ 

            $error["password"][0] = true;
            $flag = true;
            
        }

        require_once("database.php");

        if(!$flag){
            $password = md5($password);
            $query = "SELECT * FROM Users WHERE email = '$email' AND password = '$password'";
            $result = mysqli_query($myDB, $query);
        

            if(!$result){
                echo "Alguma coisa correu mal...";
                die();
            }

            elseif(mysqli_num_rows($result) != 1){ // Só pode haver um resultado
                $error["auth"][0] = true;
                $email = "";
                $password = ""; // Esvazia a variável POST. É importante fazer porque a variável POST é verificada no início se está vazia
            }

            else{
                $_SESSION["email"] = $email;
                header("Location: index.php");
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
        <title>Login</title>
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
                <a href="index.php">Início</a>
                <a href="products.php">Produtos</a>
                
                <?php

                if(empty($_SESSION)){ // O utilizador é um visitante
                    echo "<a href='register.php'>Registo</a>";
                    echo "<a href='contact.php'>Contacto</a>";
                }

                ?>

            </nav>
        </header>

        <main>
            <div class="form-box">
                <p class="form-title">Fazer login</p>
                <form action="login.php" method="post" name="form">
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

                    elseif(isset($email) && isset($password) && $error["auth"][0]){
                        echo $error["auth"][1];
                    }
                ?>
                </div>
                <input type="submit" value="Login">
                </form>
            </div>
        </main>

        <footer>
            <p>&copy; Produtos regionais de Portugal - Todos os direitos reservados</p>
        </footer>
    </body>
</html>