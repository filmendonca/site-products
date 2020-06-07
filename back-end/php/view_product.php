<?php

session_start();

if(empty($_SESSION)){
    header("Location: login.php");
    die();
}

elseif(empty($_GET)){
    header("Location: forbidden.php");
    die();
}



if(!empty($_GET)){
    $id_produto = $_GET["id"];
    $_SESSION["id_produto"] = $id_produto;
    $id_user = $_SESSION["id"];

    require_once("database.php");

    $queryView = "SELECT * FROM Produtos WHERE id_produto = '$id_produto'";

    $resultView = mysqli_query($myDB, $queryView);

    if(!$resultView){
        header("Location: error_database.php");
        die();
    }

    else{
        $rowView = mysqli_fetch_array($resultView);

        if($rowView["stock"] == 0){
            $rowView["disponivel"] = 0;
        }


        if($rowView["disponivel"] == 1){
            $rowView["disponivel"] = "Sim";
        }

        else{
            $rowView["disponivel"] = "Não";
        }
    }

    $queryClass = "SELECT * FROM Comentarios WHERE id_produto = '$id_produto'";
    $resultClass = mysqli_query($myDB, $queryClass);

    if(!$resultClass){
        header("Location: error_database.php");
        die();
    }

    else{
        $numResult = mysqli_num_rows($resultClass);

        if($numResult > 0){

            $addClass = 0;

            while($rowClass = mysqli_fetch_array($resultClass)){
                $addClass += $rowClass["classificacao"];
                // Adiciona cada valor que foi selecionado da classificação à variável
            }

            $totalClass = $addClass / $numResult;
            // Divide a soma das classificações do produto com o número de resultados encontrado
            
            $queryTotal = "UPDATE Produtos SET classificacao_total = '$totalClass' WHERE id_produto = '$id_produto'";
            $resultTotal = mysqli_query($myDB, $queryTotal);
        
            if(!$resultTotal){
                header("Location: error_database.php");
                die();
            }

        }   

    }

}

if(!empty($_POST)){
    $comentario = $_POST["comentario"];
    $classificacao = $_POST["classificacao"];

    $minComentario = 20;
    $maxComentario = 200;
    $minClassificacao = 1;
    $maxClassificacao = 5;

    // Lista de erros dos vários campos

    $error = array('comentario' => array(false, 'O comentário deve ter entre '. $minComentario. ' e '. $maxComentario. ' caracteres'),
    'classificacao' => array(false, 'A classificação só pode ser de '. $minClassificacao.' a '. $maxClassificacao),
    'existing_comment' => array(false, 'Já comentou este produto'));

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

    $queryExist = "SELECT * FROM Comentarios WHERE id_produto = '$id_produto' AND id_users = '$id_user'";
    $resultExist = mysqli_query($myDB, $queryExist);

    if(!$resultExist){
        header("Location: error_database.php");
        die();
    }

    elseif(mysqli_num_rows($resultExist) > 0){ //Só pode existir um comentário de cada utilizador para o produto
        $error["existing_comment"][0] = true;
        $flag = true;
    }



    if(!$flag){

        $queryInsert = "INSERT INTO Comentarios(id_produto, id_users, comentario, classificacao) VALUES ('$id_produto', '$id_user', '$comentario', '$classificacao')";
        $resultInsert = mysqli_query($myDB, $queryInsert);

        if(!$resultInsert){
            header("Location: error_database.php");
            die();
        }

        else{
            $_GET["comment"] = "successful";
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
        <title>Ver produto</title>
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

            <div class="view-box">
                
                <?php

                if(empty($rowView["imagem"])){ // Se o produto não tiver imagem
                    $rowView["imagem"] = "default.png";
                }

                echo 
                    "<img src=upload/".$rowView["imagem"]." alt=".$rowView["nome"]." width='500' height='500'> <br><br><br><br>  
                    Nome: <br>"
                    .$rowView['nome']. "<br>   
                    Descrição: <br>
                    <span class='text-format'>".$rowView['descricao']. "</span><br>
                    Categoria: <br>"
                    .$rowView['categoria']. "<br> 
                    Classificação: <br>";

                    if($rowView["classificacao_total"] == 0.0){ // Se ainda nenhum utilizador tiver classificado o produto
                        echo "Este produto ainda não foi classificado<br>"; 
                    }
                    else{ // Se já tiver classificado
                        $classTotal = $rowView['classificacao_total'];
                        $classTotal = str_replace('.', ',', $classTotal);
                        echo $classTotal."<br>";
                    }

                    echo "Preço: <br>";

                    if($rowView["promocao"] != 0){ // Se não for zero, o valor da promoção é calculado
                        $perc = $rowView["promocao"] / 100;
                        $valorPromocao = $rowView["preco"] * $perc;
                        $precoTotal = $rowView["preco"] - $valorPromocao;
                        $precoTotal = number_format($precoTotal, 2);
                        //Faz com que o valor representado tenha sempre 2 casas decimais
                        
                        $precoTotal = str_replace('.', ',', $precoTotal);
                        
                        echo $precoTotal."€<br>";
                    }

                    else{
                        $rowView["preco"] = str_replace('.', ',', $rowView["preco"]);
                        // Substitui o ponto do valor "preco" pela vírgula
                        echo $rowView['preco']."€<br>";
                    }

                    echo "Promoção: <br>";

                    if($rowView["promocao"] != 0){ // Se o valor da promoção não for zero
                        echo $rowView['promocao']. "%<br>"; 
                    }
                    else{ // Caso seja zero
                        echo "Não<br>"; 
                    }

                    echo "Stock: <br>"
                    .$rowView['stock']. "<br> 
                    Disponível: <br>"
                    .$rowView['disponivel']. "<br>";

                    require_once("database.php");

                    $queryShow = "SELECT * FROM Venda WHERE id_produto = '$id_produto' AND id_users = '$id_user'";
                
                    $resultShow = mysqli_query($myDB, $queryShow);
                
                    if(!$resultShow){
                        header("Location: error_database.php");
                        die();
                    }

                    elseif(mysqli_num_rows($resultShow) == 1){ // O utilizador atual é o vendedor do produto
                        echo "<a href='edit_product.php?id=$id_produto'>Alterar informação</a>";
                        echo "<a class=content-right href='remove_product.php?id=$id_produto'>Retirar produto</a>";
                    }

                    else{

                        if($rowView["disponivel"] == 1){
                            echo "<a href='buy_product.php?id=$id_produto'>Comprar produto</a>";
                        }

                        else{
                            echo "";
                        }

                    }

                ?>

            </div>

            <br><br><br><br><br><br>

            <div class="form-box">
            
            <?php

                echo "<h2>Comentários:</h2>";

                require_once("database.php");

                $queryComment = "SELECT Comentarios.*, Users.nome FROM Users
                LEFT JOIN Comentarios ON Users.id_users = Comentarios.id_users
                WHERE Comentarios.id_produto = $id_produto";

                $resultComment = mysqli_query($myDB, $queryComment);
                
                if(!$resultComment){
                    header("Location: error_database.php");
                    die();
                }

                elseif(mysqli_num_rows($resultComment) == 0){
                    echo "<br>Ainda não há comentários para este produto.";
                }

                else{

                    while ($rowComment = mysqli_fetch_array($resultComment)){

                        if($id_produto == $rowComment["id_produto"]){ 
                        // Esta condição certifica-se de que só aparecem os comentários relacionados com o produto

                            $time = $rowComment["data"];
                            $time = date('Y/m/d - G:i',strtotime($time)); // Formata a data que foi retirada da base de dados

                            echo "<br>".$rowComment["nome"]. " disse:<br>
                            <span class='text-format'>".$rowComment["comentario"]."</span><br>
                            Classificação: ".$rowComment["classificacao"]."<br>
                            Inserido em: ".$time."<br><br>";
                            

                            if($id_user == $rowComment["id_users"]){ // Verifica se foi o utilizador atual que escreveu o comentário
                                echo "<a href=edit_comment.php?id=".$id_produto.">Editar comentário</a>";
                                echo "<a class=content-right href=delete_comment.php?id=".$id_produto.">Apagar comentário</a><br><br>";
                            }

                            echo "<hr width='96%'>";

                        }

                    }
                }

            ?>
            
            </div>

            <div class="form-box">
            
  
            <p class="form-title">Escrever comentário</p>
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

                    elseif(!empty($error) && $error["existing_comment"][0]){
                        echo $error["existing_comment"][1];
                    }
                
                ?>
                </div>
                <input type="submit" value="Enviar">
                </form>

            
            </div>

        </main>

        <footer>
            <p>&copy; Produtos regionais de Portugal - Todos os direitos reservados</p>
        </footer>
    </body>
</html>

<?php

if(isset($_GET["comment"])){
    $comment = $_GET["comment"];

    switch ($comment){ // Coloca o texto de acordo com a ação feita
        case "successful":
            echo "<script>alert('Comentário adicionado com sucesso')</script>";
            break;
        case "deleted":
            echo "<script>alert('Comentário apagado com sucesso')</script>";
            break;
        case "edited":
            echo "<script>alert('Comentário editado com sucesso')</script>";
            break;
    }

}

if(isset($_GET["purchase"])){
    $purchase = $_GET["purchase"];

    switch ($purchase){ // Texto relacionado com a compra do produto
        case "success":
            echo "<script>alert('Produto adquirido com sucesso')</script>";
            break;
        case "failed":
            echo "<script>alert('Ocorreu um erro ao processar o seu pedido')</script>";
            break;
    }
}

if(isset($_GET["edit"])){
    echo "<script>alert('Informações alteradas com sucesso');</script>";
}

?>