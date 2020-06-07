<?php

session_start();

if(empty($_SESSION) || $_SESSION["tipo_user"] != 3){
// Certifica-se de que para aceder à página, o utilizador deve estar autenticado e tem de ser administrador
    
    header("Location: forbidden.php");
    die();

}

else{

    $id_user = $_SESSION["id"];

    require_once("database.php");
    
    $queryAdmin = "SELECT * FROM Users WHERE id_users <> '$id_user'";
    // Seleciona todos os utilizadores menos o próprio utilizador que tem a sessão iniciada

    $resultAdmin = mysqli_query($myDB, $queryAdmin);

    if(!$resultAdmin){
        header("Location: error_database.php");
        die();
    }

    else{
        $rowAdmin = mysqli_fetch_array($resultAdmin);
        
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Painel</title>
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

            <h2>Utilizadores: </h2>
                
                <?php

                while($rowAdmin = mysqli_fetch_array($resultAdmin)){

                    $tipo_user = $rowAdmin["tipo_user"];

                    if($tipo_user == 3){
                        $tipo_user = "Administrador";
                    }
            
                    else{
                        $tipo_user = "Normal";
                    }

                    $data_registo = $rowAdmin["data_registo"];
                    $tempo = time() - strtotime("$data_registo"); // Diferença entre a data atual e a data listada
                    $dias = floor($tempo / 86400); // Tempo a dividir pelos segundos de um dia
                    $userID = $rowAdmin["id_users"];
                    $sure = "Tem a certeza?";
                    
                    echo 
                        "<br>Nome: ".$rowAdmin["nome"]."<br>
                        ID: ".$userID."<br>
                        Email: ".$rowAdmin["email"]."<br>
                        Data de registo: ".$rowAdmin["data_registo"]."<br>
                        Total de dias inscrito: ".$dias." dias<br>
                        Tipo de utilizador: ".$tipo_user."<br>";

                        if($tipo_user != "Administrador"){
                            echo "<a href=privilege.php?user=$userID>Dar privilégios ao utilizador</a><br><br>";
                        }

                        echo "<hr width='96%'>";

                }

                ?>

            </div>

            <div class="form-box">

                <?php

                echo "<h2>Compras dos utilizadores: </h2>";

                require_once("database.php");

                $queryBuy = "SELECT Compra.*, Produtos.nome, Users.nome AS username FROM Produtos
                -- A coluna de Users.nome passa a ser 'username' de forma a não se confundir o nome do produto
                LEFT JOIN Compra ON Produtos.id_produto = Compra.id_produto
                LEFT JOIN Users ON Compra.id_users = Users.id_users
                WHERE Compra.id_users <> $id_user";


                $resultBuy = mysqli_query($myDB, $queryBuy);
                
                if(!$resultBuy){
                    header("Location: error_database.php");
                    die();
                }

                elseif(mysqli_num_rows($resultBuy) == 0){ 
                    echo "Nenhum utilizador fez uma compra.";
                }

                else{

                    while ($rowBuy = mysqli_fetch_array($resultBuy)){
                    // Produtos comprados pelos utilizadores

                        echo "<br>Nome do comprador: ".$rowBuy["username"]. "<br>
                        ID do comprador: ".$rowBuy["id_users"]."<br>
                        Nome do produto: ".$rowBuy["nome"]."<br>
                        ID do produto: ".$rowBuy["id_produto"]."<br>
                        Quantidade: ".$rowBuy["quantidade"]."<br>
                        Data de compra: ".$rowBuy["data_compra"]."<br><br>
                        <hr width='96%'>";

                    }

                }

                ?>

            </div>

            <div class="form-box">

                <?php

                echo "<h2>Produtos à venda dos utilizadores: </h2>";

                require_once("database.php");

                $querySell = "SELECT Venda.*, Produtos.nome,stock, Users.nome AS username FROM Produtos
                -- A coluna de Users.nome passa a ser 'username' de forma a não se confundir o nome do produto
                LEFT JOIN Venda ON Produtos.id_produto = Venda.id_produto
                LEFT JOIN Users ON Venda.id_users = Users.id_users
                WHERE Venda.id_users <> $id_user";

                $resultSell = mysqli_query($myDB, $querySell);

                if(!$resultSell){
                    header("Location: error_database.php");
                    die();
                }

                elseif(mysqli_num_rows($resultSell) == 0){ 
                    echo "Nenhum utilizador tem um produto à venda.";
                }

                else{

                    while ($rowSell = mysqli_fetch_array($resultSell)){
                    // Produtos postos à venda pelos utilizadores

                        echo "<br>Nome do vendedor: ".$rowSell["username"]."<br>
                        ID do vendedor: ".$rowSell["id_users"]."<br>
                        Nome do produto: ".$rowSell["nome"]."<br>
                        ID do produto: ".$rowSell["id_produto"]."<br>
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

if(isset($_GET["set"])){
    echo "<script>alert('Este utilizador agora é um administrador')</script>";
}

?>