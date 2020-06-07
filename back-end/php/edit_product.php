<?php

session_start();

if(empty($_SESSION) || empty($_GET) || $_SESSION["id_produto"] != $_GET["id"]){
/* Certifica-se de que para aceder à página, o utilizador deve estar autenticado, 
tem de aceder com um id e este id tem de ser igual ao id do produto da página onde se encontrava */

    header("Location: forbidden.php");
    die();

}

$id_produto = $_GET["id"];

if(!empty($_POST)){ // Certifica-se de que aparece sempre vazio na primeira vez

    $nome = $_POST["nome"];
    $categoria = $_POST["categoria"];
    $descricao = $_POST["descricao"];
    $preco = $_POST["preco"];
    $promocao = $_POST["promocao"];
    $quantidade = $_POST["quantidade"];

    $minNome = 3;
    $maxNome = 40;
    $minDescricao = 30;
    $maxDescricao = 200;

    // Lista de erros dos vários campos

    $error = array('nome' => array(false, 'O nome deve ter entre '. $minNome. ' e '. $maxNome. ' caracteres alfanuméricos e começar em letra maiúscula'),
    'preco' => array(false, 'Valor inválido'),
    'quantidade' => array(false, 'Valor inválido'),
    'descricao' => array(false, 'A descrição deve ter entre '. $minDescricao. ' e '. $maxDescricao. ' caracteres'),
    'promocao' => array(false, 'A percentagem tem que estar entre 0 e 99'));

    require_once("field_validation.php");

    $nome = cleanField($nome);
    $preco = cleanField($preco);
    $promocao = cleanField($promocao);
    $quantidade = cleanField($quantidade);
    $descricao = cleanField($descricao);
    $flag = false;

    if(!CheckProductName($minNome, $maxNome, $nome)){

        $error["nome"][0] = true;
        $flag = true;
        
    }

    if(!checkPrice($preco)){

        $error["preco"][0] = true;
        $flag = true;

    }

    if(!checkDiscount($promocao)){

        $error["promocao"][0] = true;
        $flag = true;

    }

    if(!checkQuantity($quantidade)){

        $error["quantidade"][0] = true;
        $flag = true;

    }

    if(!checkText($minDescricao, $maxDescricao, $descricao)){

        $error["descricao"][0] = true;
        $flag = true;
        
    }


    require_once("database.php");

    if(!$flag){

        $preco = str_replace(',', '.', $preco); 
        // Substitui a vírgula por um ponto de forma a que o número seja válido para o programa

        $query = "UPDATE Produtos SET nome = '$nome', categoria = '$categoria', 
        descricao = '$descricao', preco = '$preco', promocao = '$promocao', stock = '$quantidade' WHERE id_produto = '$id_produto'";
        $result = mysqli_query($myDB, $query);

        if(!$result){
            header("Location: error_database.php");
            die();
        }

        else{
            header("Location: edit_image.php?id=".$id_produto);
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
        <title>Editar produto</title>
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
                    echo "<p>Bem-vindo ".$nome_user."</p>";
                    echo "<a href='logout.php'><button>Logout</button></a>";
            }

            else{
                echo "<a href='login.php'><button>Login</button></a>";
            }

            ?>

            <div class="form-box">
                <p class="form-title">Editar produto</p>
                <form action="" method="post" name="form" id="add-form">
                <div class="name-box">
                    Nome: <br>
                    <input type="text" name="nome" value="<?php

                        if(!empty($error) && !$error["nome"][0]){
                            echo $nome;
                        }

                    ?>"> <br>
                    <?php 
                        if(!empty($error) && $error["nome"][0]){
                                                         
                        echo $error["nome"][1];
                        }
                    ?>
                </div>
                <div class="email-box">
                    Categoria: <br>
                    <select name="categoria">
                        <option value="Azeite">Azeite</option>
                        <option value="Compota">Compota</option>
                        <option value="Enchidos">Enchidos</option>
                        <option value="Legumes">Legumes</option>
                        <option value="Mel">Mel</option>
                        <option value="Pao">Pão</option>
                        <option value="Queijo">Queijo</option>
                        <option value="Vinho">Vinho</option>
                    </select>
                </div>
                <div class="r_password-box">
                    Descrição: <br>
                    <textarea rows="4" cols="30" name="descricao" form="add-form"> 
                    <?php

                    if(!empty($error) && !$error["descricao"][0]){
                        echo $descricao;
                    }

                    ?>
                    </textarea><br>
                <?php 

                    if(!empty($error) && $error["descricao"][0]){
                                                     
                        echo $error["descricao"][1];
                    }

                ?>
                </div>
                <div class="password-box">
                    Preço: <br>
                    <input type="number" step="0.01" name="preco" value="<?php
                    
                    if(!empty($error) && !$error["preco"][0]){
                        echo $preco;
                    }

                ?>"> &euro;<br>
                <?php 
                    if(!empty($error) && $error["preco"][0]){
                                                     
                    echo $error["preco"][1];
                }
                ?>
                </div>
                <div class="r_password-box">
                    Promoção: <br>
                    <input type="number" name="promocao" value="<?php
                    
                    if(!empty($error) && !$error["promocao"][0]){
                        echo $promocao;
                    }

                ?>"> % <br>(Caso não queira promoção, insira o valor "0")<br>
                <?php 

                    if(!empty($error) && $error["promocao"][0]){
                                                     
                        echo $error["promocao"][1];
                    }
                    
                ?>
                </div>
                <div class="name-box">
                    Quantidade: <br>
                    <input type="number" name="quantidade" value="<?php

                        if(!empty($error) && !$error["quantidade"][0]){
                            echo $quantidade;
                        }

                    ?>"> <br>
                    <?php 
                        if(!empty($error) && $error["quantidade"][0]){
                                                         
                        echo $error["quantidade"][1];
                    }
                    ?>

                </div>
                <input type="submit" value="Editar">
                </form>
                    <?php 
                    
                    echo "<a href=view_product.php?id=$id_produto>Voltar</a>";

                    ?>

            </div>
        </main>

        <footer>
            <p>&copy; Produtos regionais de Portugal - Todos os direitos reservados</p>
        </footer>
    </body>
</html>