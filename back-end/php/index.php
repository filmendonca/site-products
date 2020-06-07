<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Index</title>
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
                
                session_start();

                if(!empty($_SESSION)){
                
                    require_once("database.php");
    
                    $email = $_SESSION["email"];
                    $query = "SELECT * FROM Users WHERE email = '$email'";
    
                    $result = mysqli_query($myDB, $query);
    
                    if(!$result){
                        header("Location: error_database.php");
                        die();
                    }
    
                    else{
                        $row = mysqli_fetch_array($result); // Serve para aceder ao valor das colunas de uma entrada da tabela
                        $_SESSION["id"] = $row["id_users"];
                        $_SESSION["nome"] = $row["nome"];
                        $_SESSION["tipo_user"] = $row["tipo_user"];
                        $_SESSION["data_registo"] = $row["data_registo"];
                        $nome = $_SESSION["nome"];
                        $id_user = $_SESSION["id"];
                    }
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

            <h1>Bem-vindo ao nosso site</h1>
            <h2>Neste site, você pode comprar e vender os mais variados produtos regionais de Portugal</h2>

            <img class="index-image" src="../../front-end/img/produtos-regionais.jpg" alt="produtos-regionais">

        </main>

        <footer>
            <p>&copy Produtos regionais de Portugal - Todos os direitos reservados</p>
        </footer>
    </body>
</html>