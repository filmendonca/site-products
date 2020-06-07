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

if(!empty($_POST["action"])){

    require_once("database.php");

    $queryVenda = "DELETE FROM Venda WHERE id_produto = '$id_produto'";
    $resultVenda = mysqli_query($myDB, $queryVenda);

    if(!$resultVenda){
        header("Location: error_database.php");
        die();
    }

    $queryComment = "DELETE FROM Comentarios WHERE id_produto = '$id_produto'";
    $resultComment = mysqli_query($myDB, $queryComment);

    if(!$resultComment){
        header("Location: error_database.php");
        die();
    }

    $query = "SELECT * FROM Compra WHERE id_produto = '$id_produto'";
    $result = mysqli_query($myDB, $query);

    if(!$result){
        header("Location: error_database.php");
        die();
    }

    elseif(mysqli_num_rows($result) > 0){
        $queryRetire = "UPDATE Produtos SET retirado = 1 WHERE id_produto = '$id_produto'";
        $resultRetire = mysqli_query($myDB, $queryRetire);

        if(!$resultRetire){
            header("Location: error_database.php");
            die();
        }

        $_GET["product"] = "withdrawn";
        header("Location: products.php?product=".$_GET["product"]);
        die();
    }

    else{
        $queryRemove = "DELETE FROM Produtos WHERE id_produto = '$id_produto'";
        $resultRemove = mysqli_query($myDB, $queryRemove);

        if(!$resultRemove){
            header("Location: error_database.php");
            die();
        }

        $_GET["product"] = "removed";
        header("Location: products.php?product=".$_GET["product"]);
        die();
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Remover produto</title>
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
                
                <?php

                echo "<p class='form-title'>Remover produto</p>";

                echo "Tem a certeza de que quer remover o produto?<br><br>";

                echo "<form action='' method='post'>
                <input type='submit' name='action' value='Sim'>
                </form>
                <a href='view_product.php?id=$id_produto'><button>Não</button></a><br><br>";

                echo
                "Nota: Só pode remover inteiramente o produto se ainda nenhum utilizador tiver realizado uma compra.
                Caso contrário, o produto não aparece mais na galeria de produtos mas ainda permanece no sistema.";

                ?>

            </div>

        </main>

        <footer>
            <p>&copy; Produtos regionais de Portugal - Todos os direitos reservados</p>
        </footer>
    </body>
</html>