<?php

session_start();

//Pesquisa por texto

if(!empty($_GET["search"])){

    $search = $_GET["search"];

    $error = array('search' => array(false, 
    "<p class='no-results'>A pesquisa tem de conter pelo menos 3 caracteres e não pode ter caracteres especiais</p>"));

    require_once("field_validation.php");

    $search = cleanField($search);
    $flag = false;

    if(!checkSearch($search) || strlen($search) < 3){ 
    //Não é feita pesquisa se esta tiver menos de 3 caracteres ou caracteres especiais

        $error["search"][0] = true;
        $flag = true;
        $k = 0;
        $output[$k] = $error["search"][1];
        
    }

    if(!$flag){

        require_once("database.php");

        $querySearch = "SELECT * FROM Produtos WHERE nome LIKE '%$search%' OR descricao LIKE '%$search%'";

        $resultSearch = mysqli_query($myDB, $querySearch);

        if(!$resultSearch){
            header("Location: error_database.php");
            die();
        }

        else{
            $total = mysqli_num_rows($resultSearch);

            if($total == 0){
                $k = 0;
                $output[$k] = "<p class='no-results'>Não foram encontrados resultados</p>";
            }

            else{

                $k = 0;  // Variável que vai contar o número de produtos a mostrar

                while($rowSearch = mysqli_fetch_array($resultSearch)){

                    $rowSearch["preco"] = str_replace('.', ',', $rowSearch["preco"]);

                    $rowSearch["classificacao_total"] = str_replace('.', ',', $rowSearch["classificacao_total"]);

                    if($rowSearch["retirado"] == 0){//Só aparecem os produtos que não foram retirados

                        if(empty($rowSearch["imagem"])){ // Se o produto não tiver imagem
                            $rowSearch["imagem"] = "default.png";
                        }

                        $outputResult = "<p class='num-result'>Resultados: " .$total."</p>";

                        $viewID = "view_product.php?id=".$rowSearch["id_produto"];

                        $output[$k] = 
                        "<div class='row'>
                            <img src=upload/".$rowSearch["imagem"]." alt=".$rowSearch["nome"]." width='150' height='150'>
                            <div class='product-info'>
                                <p class='product-title'>".$rowSearch["nome"]."</p>
                                <p class='text-info'>".$rowSearch["descricao"]."</p>
                                <div class='rating'>
                                    <span>☆".$rowSearch["classificacao_total"]."</span>
                                </div>";

                                    $id_produto = $rowSearch["id_produto"];
                                    $queryComment = "SELECT * FROM Comentarios WHERE id_produto = '$id_produto'";
                                    $resultComment = mysqli_query($myDB, $queryComment);
                                    
                                    if(!$resultComment){
                                        header("Location: error_database.php");
                                        die();
                                    }

                                    else{
                                        $numComment = mysqli_num_rows($resultComment);

                                        $output[$k] .=  "<div class='comment'>
                                                <span class='comment-icon'>&#x1F5E9;</span>
                                                <span class='num-comment'>".$numComment."</span>
                                            </div>";
                                    }

                                    if($rowSearch["promocao"] != 0){ // Se não for zero, o valor da promoção é calculado
                                        $perc = $rowSearch["promocao"] / 100;
                                        $valorPromocao = $rowSearch["preco"] * $perc;
                                        $precoTotal = $rowSearch["preco"] - $valorPromocao;
                                        $precoTotal = number_format($precoTotal, 2);
                                        //Faz com que o valor representado tenha sempre 2 casas decimais

                                        $precoTotal = str_replace('.', ',', $precoTotal);
                                        
                                        $output[$k] .=  "<div class='red price'>€".$rowSearch["preco"]."</div>";
                                        $output[$k] .=  "<div class='price'>€".$precoTotal."</div>";
                                    }
                
                                    else{
                                        $output[$k] .=  "<div class='price'>€".$rowSearch["preco"]."</div>";
                                    }

                                    if($rowSearch["disponivel"] == 1){
                                        $output[$k] .=  "<p class='green-disp'>Disponível</p>";
                                    }

                                    else{
                                        $output[$k] .=  "<p class='red-disp'>Não disponível</p>";
                                    }

                        $output[$k] .= "<a href=$viewID>Ver produto</a>
                            </div>
                        </div>";

                        $k++;
                    }

                    else{
                        $output[$k] = "<p class='no-results'>Não foram encontrados resultados</p>";
                    }
                    
                }
            }
        }

    }   
}

elseif(isset($_GET["search"]) && $_GET["search"] == ""){ // Se o campo de pesquisa estiver vazio não ocorre pesquisa
    header("Location: products.php");
    die();
}



// Pesquisa por atributos

if(isset($_GET["order"])){

    $and = false; // Serve para dividir a pesquisa de cada atributo

    $queryOrder = "SELECT * FROM Produtos WHERE ";

    if(isset($_GET["categoria"])){

        if(count($_GET["categoria"]) == 1){// Se se só selecionar uma categoria

            foreach($_GET["categoria"] as $categoria){
                $queryOrder .= "categoria = '$categoria' ";
            }
    
            $and = true;
        }

        elseif(count($_GET["categoria"]) > 1){// Se se selecionar mais de uma categoria

            foreach($_GET["categoria"] as $categoria){
                $queryOrder .= "categoria = '$categoria' ";
                $queryOrder .= "OR ";
            }

            $queryOrder = substr($queryOrder, 0, -3); //Retira os 3 útlimos caracteres da string (OR )
    
            $and = true;
        }

    }

    if(isset($_GET["class"])){

        $classificacao = $_GET["class"];

        if($and){
            $queryOrder .= "AND ";
        }
        
        if($classificacao == 0){
            $queryOrder .= "classificacao_total = $classificacao ";
        }
        elseif($classificacao > 0){
            $queryOrder .= "classificacao_total >= $classificacao ";
        }

        $and = true;
    }

    if(isset($_GET["price"])){

        $preco = $_GET["price"];
        $valorPreco = explode("-", $preco); // Divide os valores e transforma-os num array

        if($and){
            $queryOrder .= "AND ";
        }

        $queryOrder .= "preco BETWEEN $valorPreco[0] AND $valorPreco[1] ";
        $and = true;
    }
    
    if(isset($_GET["prom"])){

        $promocao = $_GET["prom"];

        if($and){
            $queryOrder .= "AND ";
        }

        if($promocao == "no"){
            $queryOrder .= "promocao = 0 ";
        }
        elseif($promocao == "yes"){
            $queryOrder .= "promocao > 0 ";
        }

        $and = true;
    }

    if(isset($_GET["disp"])){

        $disponivel = $_GET["disp"];

        if($and){
            $queryOrder .= "AND ";
        }

        $queryOrder .= "disponivel = $disponivel";

    }

    require_once("database.php");
    
    $resultOrder = mysqli_query($myDB, $queryOrder);

    if(!$resultOrder){
        header("Location: error_database.php");
        die();
    }

    else{
        $total = mysqli_num_rows($resultOrder);

        if($total == 0){
            $k = 0;
            $output[$k] = "<p class='no-results'>Não foram encontrados resultados</p>";
        }

        else{

            $k = 0;// Variável que vai contar o número de produtos a mostrar

            while ($rowOrder = mysqli_fetch_array($resultOrder)){
                            
                $rowOrder["preco"] = str_replace('.', ',', $rowOrder["preco"]);

                $rowOrder["classificacao_total"] = str_replace('.', ',', $rowOrder["classificacao_total"]);

                if($rowOrder["retirado"] == 0){//Só aparecem os produtos que não foram retirados

                    if(empty($rowOrder["imagem"])){ // Se o produto não tiver imagem
                        $rowOrder["imagem"] = "default.png";
                    }

                    $outputResult = "<p class='num-result'>Resultados: " .$total."</p>";

                    $output[$k] =
                    "<div class='row'>
                        <img src=upload/".$rowOrder["imagem"]." alt=".$rowOrder["nome"]." width='150' height='150'>
                        <div class='product-info'>
                            <p class='product-title'>".$rowOrder["nome"]."</p>
                            <p class='text-info'>".$rowOrder["descricao"]."</p>
                            <div class='rating'>
                                <span>☆".$rowOrder["classificacao_total"]."</span>
                            </div>";

                            require_once("database.php");

                            $id_produto = $rowOrder["id_produto"];

                            $queryComment = "SELECT * FROM Comentarios WHERE id_produto = '$id_produto'";
                            $resultComment = mysqli_query($myDB, $queryComment);
                            
                            if(!$resultComment){
                                header("Location: error_database.php");
                                die();
                            }

                            else{
                                $numComment = mysqli_num_rows($resultComment);

                                $output[$k] .= "<div class='comment'>
                                        <span class='comment-icon'>&#x1F5E9;</span>
                                        <span>".$numComment."</span>
                                    </div>";
                            }

                            if($rowOrder["promocao"] != 0){ // Se não for zero, o valor da promoção é calculado
                                $perc = $rowOrder["promocao"] / 100;
                                $valorPromocao = $rowOrder["preco"] * $perc;
                                $precoTotal = $rowOrder["preco"] - $valorPromocao;
                                $precoTotal = number_format($precoTotal, 2);
                                //Faz com que o valor representado tenha sempre 2 casas decimais

                                $precoTotal = str_replace('.', ',', $precoTotal);
                                
                                $output[$k] .= "<div class='red price'>€".$rowOrder["preco"]."</div>";
                                $output[$k] .= "<div class='price'>€".$precoTotal."</div>";
                            }

                            else{
                                $output[$k] .= "<div class='price'>€".$rowOrder["preco"]."</div>";
                            }

                            if($rowOrder["disponivel"] == 1){
                                $output[$k] .= "<p class='green-disp'>Disponível</p>";
                            }

                            else{
                                $output[$k] .= "<p class='red-disp'>Não disponível</p>";
                            }

                            $output[$k] .= "<a href=view_product.php?id=".$rowOrder["id_produto"].">Ver produto</a>
                        </div>
                    </div>";

                    $k++;
                }

                else{
                    $output[$k] = "<p class='no-results'>Não foram encontrados resultados</p>";
                }
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
        <title>Produtos</title>
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
                        echo "<a class='log-format' href='logout.php'><button>Logout</button></a>";
                }

                else{
                    echo "<a class='log-format'href='login.php'><button>Login</button></a>";
                }

            ?>

            <aside>
                <form action="" method="get">
                    <p>Categoria:</p>
                    <input type="checkbox" name="categoria[]" value="Azeite">Azeite
                    <br>
                    <input type="checkbox" name="categoria[]" value="Compota">Compota
                    <br>
                    <input type="checkbox" name="categoria[]" value="Enchidos">Enchidos
                    <br>
                    <input type="checkbox" name="categoria[]" value="Legumes">Legumes
                    <br>
                    <input type="checkbox" name="categoria[]" value="Mel">Mel
                    <br>
                    <input type="checkbox" name="categoria[]" value="Pao">Pão
                    <br>
                    <input type="checkbox" name="categoria[]" value="Queijo">Queijo
                    <br>
                    <input type="checkbox" name="categoria[]" value="Vinho">Vinho
                    <br>
                    <p>Classificação:</p>
                    <input type="radio" name="class" 
                    <?php if(isset($_GET["class"]) && $_GET["class"] == 0){echo "checked";}?> value="0">0 (Não classificado)
                    <br>
                    <input type="radio" name="class"
                    <?php if(isset($_GET["class"]) && $_GET["class"] == 1){echo "checked";}?> value="1">+1
                    <br>
                    <input type="radio" name="class"
                    <?php if(isset($_GET["class"]) && $_GET["class"] == 2){echo "checked";}?> value="2">+2
                    <br>
                    <input type="radio" name="class"
                    <?php if(isset($_GET["class"]) && $_GET["class"] == 3){echo "checked";}?> value="3">+3
                    <br>
                    <input type="radio" name="class"
                    <?php if(isset($_GET["class"]) && $_GET["class"] == 4){echo "checked";}?> value="4">+4
                    <br>
                    <p>Preço:</p>
                    <input type="radio" name="price"
                    <?php if(isset($_GET["price"]) && $_GET["price"] == "0-10"){echo "checked";}?> value="0-10">0-10
                    <br>
                    <input type="radio" name="price"
                    <?php if(isset($_GET["price"]) && $_GET["price"] == "10-30"){echo "checked";}?> value="10-30">10-30
                    <br>
                    <input type="radio" name="price"
                    <?php if(isset($_GET["price"]) && $_GET["price"] == "30-60"){echo "checked";}?> value="30-60">30-60
                    <br>
                    <input type="radio" name="price"
                    <?php if(isset($_GET["price"]) && $_GET["price"] == "60-100"){echo "checked";}?> value="60-100">60-100
                    <br>
                    <input type="radio" name="price"
                    <?php if(isset($_GET["price"]) && $_GET["price"] == "101-10000"){echo "checked";}?> value="101-10000">+100
                    <br>
                    <p>Promoção:</p>
                    <input type="radio" name="prom"
                    <?php if(isset($_GET["prom"]) && $_GET["prom"] == "yes"){echo "checked";}?> value="yes">Sim
                    <br>
                    <input type="radio" name="prom"
                    <?php if(isset($_GET["prom"]) && $_GET["prom"] == "no"){echo "checked";}?> value="no">Não
                    <br>
                    <p>Disponível</p>
                    <input type="radio" name="disp"
                    <?php if(isset($_GET["disp"]) && $_GET["disp"] == 1){echo "checked";}?> value="1">Sim
                    <br>
                    <input type="radio" name="disp"
                    <?php if(isset($_GET["disp"]) && $_GET["disp"] == 0){echo "checked";}?> value="0">Não
                    <br>
                    <input type="submit" value="Ordenar" name="order">
                </form>
            </aside>
            <div class="search-box">
                <form action="products.php" method="get">
                    <button type="submit"><div class="search-icon">&#9906</div></button>
                    <input type="text" placeholder="Pesquisa" name="search">
                </form>
            </div>
            
            <?php
    
                if(!empty($_SESSION["email"])){
                    echo "<a class='product-add' href='add_product.php'>Adicionar produto</a>";
                }
            
            ?>

            <?php

                if(!empty($outputResult)){
                    echo $outputResult; // Número de resultados da pesquisa
                }

            ?>

            <div class="center-box">

                <?php

                require_once("database.php");

                $queryDisplay = "SELECT * FROM Produtos";
                $resultDisplay = mysqli_query($myDB, $queryDisplay);
                
                if(!$resultDisplay){
                    header("Location: error_database.php");
                    die();
                }
                
                elseif(!isset($_GET["search"]) && !isset($_GET["order"])){
                // Se não tiver sido efetuada pesquisa por texto ou por atributos

                    while ($rowDisplay = mysqli_fetch_array($resultDisplay)){
                    
                        $rowDisplay["preco"] = str_replace('.', ',', $rowDisplay["preco"]);

                        $rowDisplay["classificacao_total"] = str_replace('.', ',', $rowDisplay["classificacao_total"]);

                        if($rowDisplay["retirado"] == 0){//Só aparecem os produtos que não foram retirados

                            if(empty($rowDisplay["imagem"])){ // Se o produto não tiver imagem
                                $rowDisplay["imagem"] = "default.png";
                            }

                            echo
                            "<div class='row'>
                                <img src=upload/".$rowDisplay["imagem"]." alt=".$rowDisplay["nome"]." width='150' height='150'>
                                <div class='product-info'>
                                    <p class='product-title'>".$rowDisplay["nome"]."</p>
                                    <p class='text-info'>".$rowDisplay["descricao"]."</p>
                                    <div class='rating'>
                                        <span>☆".$rowDisplay["classificacao_total"]."</span>
                                    </div>";

                                    require_once("database.php");

                                    $id_produto = $rowDisplay["id_produto"];
                                    $queryComment = "SELECT * FROM Comentarios WHERE id_produto = '$id_produto'";
                                    $resultComment = mysqli_query($myDB, $queryComment);
                                    
                                    if(!$resultComment){
                                        header("Location: error_database.php");
                                        die();
                                    }

                                    else{
                                        $numComment = mysqli_num_rows($resultComment);
                                        // Número de comentários de cada produto
                                        echo "<div class='comment'>
                                                <span class='comment-icon'>&#x1F5E9;</span>
                                                <span>".$numComment."</span>
                                            </div>";
                                    }

                                    if($rowDisplay["promocao"] != 0){ // Se não for zero, o valor da promoção é calculado
                                        $perc = $rowDisplay["promocao"] / 100;
                                        $valorPromocao = $rowDisplay["preco"] * $perc;
                                        $precoTotal = $rowDisplay["preco"] - $valorPromocao;
                                        $precoTotal = number_format($precoTotal, 2);
                                        //Faz com que o valor representado tenha sempre 2 casas decimais

                                        $precoTotal = str_replace('.', ',', $precoTotal);
                                        
                                        echo "<div class='red price'>€".$rowDisplay["preco"]."</div>";
                                        echo "<div class='price'>€".$precoTotal."</div>";
                                    }
                
                                    else{// Caso o valor da promoção seja zero
                                        echo "<div class='price'>€".$rowDisplay["preco"]."</div>";
                                    }

                                    if($rowDisplay["disponivel"] == 1){
                                        echo "<p class='green-disp'>Disponível</p>";
                                    }

                                    else{
                                        echo "<p class='red-disp'>Não disponível</p>";
                                    }

                            echo    "<a href=view_product.php?id=".$rowDisplay["id_produto"].">Ver produto</a>
                                </div>
                            </div>";

                        }
                
                    }

                }

                else{
                    if($k > 0){ // Se aparecerem 1 ou mais resultados
                        for($i=0; $i < $k; $i++){ // Escreve cada entrada da pesquisa por texto ou por atributos
                            echo $output[$i];
                        }
                    }
                    elseif($k == 0){ // Caso não haja resultados
                        for($i=0; $i == $k; $i++){
                            echo $output[$i];
                        }
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

if(isset($_GET["product"])){
    $product = $_GET["product"];

    switch ($product){ // Texto relacionado com a remoção do produto
        case "withdrawn":
            echo "<script>alert('O seu produto foi retirado da galeria de produtos')</script>";
            break;
        case "removed":
            echo "<script>alert('O seu produto foi removido')</script>";
            break;
    }

}


if(isset($_GET["add"])){
    echo "<script>alert('Produto adicionado com sucesso')</script>";
}

?>