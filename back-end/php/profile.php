<?php

session_start();

if(empty($_SESSION) || empty($_GET) || $_SESSION["id"] != $_GET["id"]){ 
/* Certifica-se de que para aceder à página, o utilizador deve estar autenticado, 
   tem de aceder com um id e este id tem de ser o seu próprio id */
    
    header("Location: forbidden.php");
    die();

}

else{

    $id_user = $_SESSION["id"];

    require_once("database.php");
    
    $query = "SELECT * FROM Users WHERE id_users = '$id_user'";

    $result = mysqli_query($myDB, $query);

    if(!$result){
        header("Location: error_database.php");
        die();
    }

    else{
        $row = mysqli_fetch_array($result); // Serve para aceder às colunas de uma entrada da tabela
        
        $_SESSION["nome"] = $row["nome"];
        $_SESSION["email"] = $row["email"];
        $nome = $_SESSION["nome"];
        $email = $_SESSION["email"];
        $data_registo = $_SESSION["data_registo"];
    }

    $tempo = time() - strtotime("$data_registo"); // Diferença entre a data atual e a data listada
    $dias = floor($tempo / 86400); // Tempo a dividir pelos segundos de um dia
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Perfil</title>
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

                echo "<h2>Dados pessoais: </h2><br>
                    Nome: $nome<br>
                    Email: $email<br>
                    Data de registo: $data_registo<br>
                    Total de dias inscrito: $dias dias<br><br>";

                echo "<a href=change_data.php?id=".$id_user.">Alterar dados pessoais</a>";

                ?>

            </div>

            <div class="form-box">

                <?php

                echo "<h2>Histórico de compras: </h2>";

                require_once("database.php");

                $queryBuy = "SELECT Compra.*, Produtos.nome FROM Produtos
                LEFT JOIN Compra ON Produtos.id_produto = Compra.id_produto
                WHERE Compra.id_users = $id_user";

                $resultBuy = mysqli_query($myDB, $queryBuy);
                
                if(!$resultBuy){
                    header("Location: error_database.php");
                    die();
                }

                elseif(mysqli_num_rows($resultBuy) == 0){ 
                    echo "<br>Ainda não fez nenhuma compra.";
                }

                else{

                    while ($rowBuy = mysqli_fetch_array($resultBuy)){
                    // Produtos adquiridos pelo utilizador e a sua quantidade

                        echo "<br>Nome do produto: " .$rowBuy["nome"]."<br>
                        ID: ".$rowBuy["id_produto"]."<br>
                        Quantidade: ".$rowBuy["quantidade"]."<br>
                        Data de compra: ".$rowBuy["data_compra"]."<br><br>
                        <hr width='96%'>";

                    }

                }

                ?>

            </div>

            <div class="form-box">

                <?php

                echo "<h2>Produtos à venda: </h2>";

                require_once("database.php");

                $querySell = "SELECT Venda.*, Produtos.nome,stock FROM Produtos
                LEFT JOIN Venda ON Produtos.id_produto = Venda.id_produto
                WHERE Venda.id_users = $id_user";

                $resultSell = mysqli_query($myDB, $querySell);
                
                if(!$resultSell){
                    header("Location: error_database.php");
                    die();
                }

                elseif(mysqli_num_rows($resultSell) == 0){ 
                    echo "<br>Não tem nenhum produto à venda.";
                }

                else{

                    while ($rowSell = mysqli_fetch_array($resultSell)){
                    // Produtos colocados à venda pelo utilizador

                        echo "<br>Nome do produto: " .$rowSell["nome"]."<br>
                        ID: ".$rowSell["id_produto"]."<br>
                        Stock: ".$rowSell["stock"]."<br>
                        Data de inserção: ".$rowSell["data_insercao"]."<br><br>";

                        echo "<a href=view_product.php?id=".$rowSell["id_produto"].">Ver produto</a><br><br>
                        <hr width='96%'>";

                    }

                }

                ?>

            </div>

        </main>

        <footer>
            <p>&copy; Produtos regionais de Portugal - Todos os direitos reservados</p>
        </footer>
    </body>
</html>

<?php

if(isset($_GET["change"])){
    echo "<script>alert('Dados alterados com sucesso')</script>";
}

?>