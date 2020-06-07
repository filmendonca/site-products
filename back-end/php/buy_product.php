<?php

session_start();

if(empty($_SESSION) || empty($_GET) || $_SESSION["id_produto"] != $_GET["id"]){
/* Certifica-se de que para aceder � p�gina, o utilizador deve estar autenticado, 
   tem de aceder com um id e este id tem de ser igual ao id do produto da p�gina onde se encontrava */

    header("Location: forbidden.php");
    die();

}

$id_produto = $_GET["id"];
$id_user = $_SESSION["id"];

if(!empty($_POST["quantidade"]) && isset($_POST["action_calcular"])){

    $quantidade = $_POST["quantidade"];

    $error = array('quantidade' => array(false, 'Valor inv�lido'),
    'quantidade_maior' => array(false, 'A quantidade inserida � maior do que o stock'));

    require_once("field_validation.php");

    $quantidade = cleanField($quantidade);
    $flag = false;


    if(!checkQuantity($quantidade)){

        $error["quantidade"][0] = true;
        $flag = true;

    }

    require_once("database.php");

    $queryCheck = "SELECT * FROM Produtos WHERE id_produto = '$id_produto' AND stock >= '$quantidade'";

    $resultCheck = mysqli_query($myDB, $queryCheck);

    if(!$resultCheck){
        header("Location: error_database.php");
        die();
    }

    elseif(mysqli_num_rows($resultCheck) == 0){ //Se a quantidade for maior do que o stock d� erro

        $error["quantidade_maior"][0] = true;
    }

}


if(!empty($_POST["quantidade"]) && isset($_POST["action_comprar"]) && $_POST["quantidade"] > 0){

    if(!$flag){

        $quantidade = $_POST["quantidade"];

        require_once("database.php");
        
            $queryProd = "INSERT INTO Compra(id_produto, id_users, quantidade) VALUES ('$id_produto', '$id_user', '$quantidade')";
            //Cria uma nova entrada de compra
            $resultProd = mysqli_query($myDB, $queryProd);

            if(!$resultProd){
                $status = "failed";
                header("Location: view_product.php?id=".$id_produto."&purchase=".$status);
                die();
            }

            $queryStock = "UPDATE Produtos SET stock = stock - '$quantidade' WHERE id_produto = '$id_produto'";
            // Retira ao stock a quantidade comprada
            $resultStock = mysqli_query($myDB, $queryStock);

            if(!$resultStock){
                $status = "failed";
                header("Location: view_product.php?id=".$id_produto."&purchase=".$status);
                die();
            }

            $queryDisp = "UPDATE Produtos SET disponivel = 0 WHERE stock = 0 AND id_produto = '$id_produto'";
            // Se o stock ficar vazio, o produto n�o fica mais dispon�vel
            $resultDisp = mysqli_query($myDB, $queryDisp);

            if(!$resultDisp){
                $status = "failed";
                header("Location: view_product.php?id=".$id_produto."&purchase=".$status);
                die();
            }

            $status = "success";
            header("Location: view_product.php?id=".$id_produto."&purchase=".$status);
            die();
        

    }
}


?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Comprar produto</title>
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
                    $id_user = $_SESSION["id"];
                    
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
                <p class="form-title">Comprar produto</p>
                <div class="name-box">

                    Pre�o: 
                    <?php
                    
                        if(!empty($_POST["quantidade"]) && (!empty($error) && !$error["quantidade_maior"][0])){

                            $quantidade = $_POST["quantidade"];
                            require_once("database.php");

                            $queryPrice = "SELECT * FROM Produtos WHERE id_produto = '$id_produto'";
                            $resultPrice = mysqli_query($myDB, $queryPrice);

                            if(!$resultPrice){
                                header("Location: error_database.php");
                                die();
                            }

                            $rowPrice = mysqli_fetch_array($resultPrice);

                            if($rowPrice["promocao"] != 0){ // Se n�o for zero, o valor da promo��o � calculado
                                $perc = $rowPrice["promocao"] / 100;
                                $valorPromocao = $rowPrice["preco"] * $perc;
                                $precoProm = $rowPrice["preco"] - $valorPromocao;
                                $totalPrice = $precoProm * $quantidade;
                                
                                if(is_numeric($totalPrice) && $totalPrice > 0){
                                    // S� pode ser um n�mero e tem de ser maior do que zero
                                        $totalPrice = str_replace('.', ',', $totalPrice);
                                        echo $totalPrice." �";
                                    }
        
                                else{
                                    echo "";
                                }
                            }

                            else{
                                $totalPrice = $rowPrice["preco"] * $quantidade;
                                // Pre�o total � igual ao valor da coluna do produto vezes o valor da quantidade inserido
    
                                if(is_numeric($totalPrice) && $totalPrice > 0){
                                // S� pode ser um n�mero e tem de ser maior do que zero
                                    $totalPrice = str_replace('.', ',', $totalPrice);
                                    echo $totalPrice." �";
                                }
    
                                else{
                                    echo "";
                                }

                            }

                        }
                        
                    ?>
                </div>
                
                <form action="" method="post" name="form">
                <div class="name-box">
                    Quantidade: <br>
                    <input type="number" name="quantidade" value="<?php

                        if(!empty($error) && !$error["quantidade"][0]){
                            echo $quantidade;
                        }

                        elseif(!empty($_POST["quantidade"])){
                            echo $quantidade;
                        }

                    ?>"> 
                    <button type="submit" name="action_calcular" value="calcular">Calcular</button>

                    <?php

                        if(isset($_POST["action_calcular"]) && (!empty($error) && !$error["quantidade_maior"][0])){

                            echo "<button type='submit' name='action_comprar' value='comprar'>Comprar</button>";

                        }

                        elseif(isset($_POST["action_calcular"]) && (!empty($error) && $error["quantidade"][0])){

                            echo "<button type='submit' name='action_comprar' value='comprar'>Comprar</button>";

                        }

                    ?>

                    <br>
                    <?php 
                        if(!empty($error) && $error["quantidade"][0]){                                                         
                        echo $error["quantidade"][1];
                        }

                        elseif(!empty($error) && $error["quantidade_maior"][0]){
                            echo $error["quantidade_maior"][1];
                        }
                    ?>

                </div>
                </form>

                <br>
                <?php 
                    
                    echo "<a href=view_product.php?id=$id_produto>Voltar</a>";

                ?>

                </div>

            </div>
        </main>

        <footer>
            <p>&copy; Produtos regionais de Portugal - Todos os direitos reservados</p>
        </footer>
    </body>
</html>