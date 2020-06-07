<?php

session_start();

if(empty($_SESSION) || empty($_GET)){

    header("Location: forbidden.php");
    die();

}

$id_produto = $_GET["id"];
$id_user = $_SESSION["id"];


if(isset($_FILES['image'])){
    $file_name = $_FILES['image']['name'];
    $file_size = $_FILES['image']['size'];
    $file_temp = $_FILES['image']['tmp_name'];
    $file_type = $_FILES['image']['type'];
    $file_ext = strtolower(end(explode('.',$_FILES['image']['name'])));
    $extensions = array("jpeg","jpg","png");// Extensões permitidas

    if(!in_array($file_ext,$extensions)){ // Caso a extensão do ficheiro não seja do tipo permitido
        echo "<script>alert('A extensão do ficheiro só pode ser JPEG, JPG, ou PNG')</script>";
    }

    elseif($file_size > 2097152 || $file_size == 0) {
        echo "<script>alert('O tamanho da imagem não pode ser maior do que 2 MB')</script>";
    }

    else{

        require_once("database.php");

        $queryImage = "SELECT * FROM Produtos WHERE imagem = '$file_name'";
        $resultImage = mysqli_query($myDB, $queryImage);

        if(!$resultImage){
            header("Location: error_database.php");
            die();
        }

        elseif(mysqli_num_rows($resultImage) > 0){ // Se já existir uma imagem com o mesmo nome
            $ts_file_name = date('m-d-Y_H-i-s')."_".$file_name;
            move_uploaded_file($file_temp,"upload/".$ts_file_name);
            $imagem = $ts_file_name;

            require_once("database.php");

            $queryInsert = "UPDATE Produtos SET imagem = '$imagem' WHERE id_produto = '$id_produto'";
            $resultInsert = mysqli_query($myDB, $queryInsert);

            if(!$resultInsert){
                header("Location: error_database.php");
                die();
            }

            else{
                $_GET["add"] = "success";
                header("Location: products.php?add=".$_GET["add"]);
                die();
            }
        }
        
        else{
            move_uploaded_file($file_temp,"upload/".$file_name);
            $imagem = $file_name;

            require_once("database.php");

            $queryChange = "UPDATE Produtos SET imagem = '$imagem' WHERE id_produto = '$id_produto'";
            $resultChange = mysqli_query($myDB, $queryChange);

            if(!$resultChange){
                header("Location: error_database.php");
                die();
            }

            else{
                $_GET["add"] = "success";
                header("Location: products.php?add=".$_GET["add"]);
                die();
            }
        }
    }
}

?>



<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Adicionar imagem</title>
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

                    $nome_user = $_SESSION["nome"];
                    
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

            <div class="form-box">
                <p class="form-title">Adicionar imagem</p>
                <form action = "" method = "post" enctype = "multipart/form-data">
                <div class="name-box">
                Selecione a imagem do produto: <br>
                <input type = "file" name = "image" />
                </div>
                <input type = "submit" value="Enviar"/>
                <br>
                </form>
                

            </div>
        </main>

        <footer>
            <p>&copy; Produtos regionais de Portugal - Todos os direitos reservados</p>
        </footer>
    </body>
</html>