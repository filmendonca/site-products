<?php

session_start();

if(!empty($_SESSION)){
    $email_session = $_SESSION["email"];
}

if(!empty($_POST)){
    $email = $_POST["email"];
    $subject = $_POST["subject"];
    $message = $_POST["message"];

    $minTexto = 30;
    $maxTexto = 200;
    $minAssunto = 5;
    $maxAssunto = 30;

    // Lista de erros dos vários campos

    $error = array('message' => array(false, 'A mensagem deve ter entre '. $minTexto. ' e '. $maxTexto. ' caracteres'),
    'email' => array(false, 'Email inválido'),
    'subject' => array(false, 'O assunto deve ter entre '. $minAssunto.' e '. $maxAssunto. ' caracteres'),
    'email_diferente' => array(false, 'O email inserido é diferente do seu email'));

    require_once("field_validation.php");

    $message = cleanField($message);
    $email = cleanField($email);
    $subject = cleanField($subject);
    $flag = false; //Serve para ver se existem erros.

    if(!checkText($minTexto, $maxTexto, $message)){

        $error["message"][0] = true;
        $flag = true;
        
    }

    if(!checkEmail($email)){

        $error["email"][0] = true;
        $flag = true;

    }

    if(!checkText($minAssunto, $maxAssunto, $subject)){

        $error["subject"][0] = true;
        $flag = true;
        
    }

    if($email != $email_session){

        $error["email_diferente"][0] = true;
        $flag = true;
    }

    if(empty($_SESSION)){
        $code = 41;
        header("Location: register.php?error=".$code);
        die();
    }

    if(!$flag){

        $header = "MIME-Version: 1.0 \r \n";

        $header .= "Content-type text/html; charset = iso-8859-1 \r \n";
    
        $header .= "From: $email \r \n";
    
        if(mail("fil40320@gmail.com", $subject, $message, $header)){
            echo "<script>alert('Mensagem enviada')</script>";
        }
    
        else{
            echo "<script>alert('Erro ao enviar email')</script>";
        }
    }

}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Contacto</title>
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

                if(!empty($_SESSION)){

                    $nome = $_SESSION["nome"];
                    $id_user = $_SESSION["id"];
                    
                }


                if(empty($_SESSION)){ // O utilizador é um visitante
                    echo "<a href='register.php'>Registo</a>";
                    echo "<a href='contact.php'>Contacto</a>";
                }

                elseif($_SESSION["tipo_user"] == 3){ // O utilizador é um administrador
                    echo "<a href=profile.php?id=$id_user>Perfil</a>";
                    echo "<a href='indicators.php'>Painel</a>";
                }

                else{ // O utilizador é um comprador/vendedor
                    echo "<a href=profile.php?id=$id_user>Perfil</a>";
                    echo "<a href='contact.php'>Contacto</a>";
                }

                ?>

            </nav>
        </header>

        <main>

            <?php

            if(!empty($_SESSION)){
                    echo "<p>Bem-vindo ".$nome."</p>";
                    echo "<a href='logout.php'><button>Logout</button></a>";
            }

            else{
                echo "<a href='login.php'><button>Login</button></a>";
            }

            ?>

            <div class="form-box">
                <p class="form-title">Contacto</p>
                <form action="contact.php" method="post" name="form" id="contact-form">
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

                        elseif(!empty($error) && $error["email_diferente"][0]){
                            echo $error["email_diferente"][1];
                        }

                    ?>
                </div>
                <div class="password-box">
                    Assunto: <br>
                    <input type="text" name="subject" value="<?php
                    
                    if(!empty($error) && !$error["subject"][0]){
                        echo $subject;
                    }

                ?>"> <br>
                <?php 
                    if(!empty($error) && $error["subject"][0]){
                                                     
                    echo $error["subject"][1];
                }
                ?>
                </div>
                <div class="r_password-box">
                    Conteúdo: <br>
                    <textarea rows="4" cols="30" name="message" form="contact-form">
                    
                    <?php

                        if(!empty($error) && !$error["message"][0]){
                            echo $message;
                        }

                    ?>
                    
                    </textarea><br>
                <?php 

                    if(!empty($error) && $error["message"][0]){
                                                     
                        echo $error["message"][1];
                    }

                ?>
                </div>
                <input type="submit" value="Enviar" name="submit">
                </form>
            </div>
        </main>

        <footer>
            <p>&copy; Produtos regionais de Portugal - Todos os direitos reservados</p>
        </footer>
    </body>
</html>