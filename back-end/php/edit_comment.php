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


if(!empty($_POST)){
    $comentario = $_POST["comentario"];
    $classificacao = $_POST["classificacao"];

    $minComentario = 30;
    $maxComentario = 200;
    $minClassificacao = 1;
    $maxClassificacao = 5;

    // Lista de erros dos vários campos

    $error = array('comentario' => array(false, 'O comentário deve ter entre '. $minComentario. ' e '. $maxComentario. ' caracteres'),
    'classificacao' => array(false, 'A classificação só pode ser de '. $minClassificacao.' a '. $maxClassificacao));

    require_once("field_validation.php");

    $comentario = cleanField($comentario);
    $classificacao = cleanField($classificacao);
    $flag = false; //Serve para ver se existem erros.

    if(!checkText($minComentario, $maxComentario, $comentario)){

        $error["comentario"][0] = true;
        $flag = true;
        
    }

    if(!checkClassification($minClassificacao, $maxClassificacao, $classificacao)){

        $error["classificacao"][0] = true;
        $flag = true;

    }

    require_once("database.php");

    if(!$flag){

        $queryEdit = "UPDATE Comentarios SET comentario = '$comentario', classificacao = '$classificacao' 
        WHERE id_produto = '$id_produto' AND id_users = '$id_user'";
        $resultEdit = mysqli_query($myDB, $queryEdit);

        if(!$resultEdit){
            header("Location: error_database.php");
            die();
        }

        else{
            $_GET["comment"] = "edited";
            header("Location: view_product.php?id=".$id_produto."&comment=".$_GET["comment"]);
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
        <title>Editar comentário</title>
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
            
  
            <p class="form-title">Editar comentário</p>
                <form action="" method="post" name="form" id="comment-form">
                <div class="password-box">
                    Classificação: <br>
                    <input type="number" name="classificacao" value="<?php
                    
                    if(!empty($error) && !$error["classificacao"][0]){
                        echo $classificacao;
                    }

                ?>"> <br>
                <?php 
                    if(!empty($error) && $error["classificacao"][0]){
                                                     
                    echo $error["classificacao"][1];
                }
                ?>
                </div>
                <div class="r_password-box">
                    Comentário: <br>
                    <textarea rows="4" cols="30" name="comentario" form="comment-form"> 
                    <?php

                        if(!empty($error) && !$error["comentario"][0]){
                            echo $comentario;
                        }

                    ?>
                    
                    </textarea><br>
                <?php 

                    if(!empty($error) && $error["comentario"][0]){
                                                     
                        echo $error["comentario"][1];
                    }
                
                ?>
                </div>
                <input type="submit" value="Editar">
                </form>

            
            </div>

        </main>

        <footer>
            <p>&copy; Produtos regionais de Portugal - Todos os direitos reservados</p>
        </footer>
    </body>
</html>