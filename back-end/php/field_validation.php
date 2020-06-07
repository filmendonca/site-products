<?php

    function CheckStringAndLength($min, $max, $field){

        $min--;
        $max--;
        $expression = '/^[A-Z][a-z\s]{'.$min.','.$max.'}$/';

        if(preg_match($expression, $field)){
            return(true);
        }

        else{
            return(false);
        }
    }

    function cleanField($field){
        $field = trim($field);
        $field = strip_tags($field);
        $field = stripslashes($field);

        return ($field);
    }

    function checkEmail($field){
        $field = filter_var($field, FILTER_SANITIZE_EMAIL);
        if(!filter_var($field, FILTER_VALIDATE_EMAIL)){
            return(false);
        }
        else{
            return(true);
        }
    }

    function checkPassword($minPass, $maxPass, $field){

        $expressionPass = '/^[A-z0-9_-]{'.$minPass.','.$maxPass.'}$/';

        if(!preg_match($expressionPass, $field)){
            return(false);
        }

        else{
            return(true);
        }
    }

    function CheckProductName($min, $max, $field){ // Função para verificar o nome do produto introduzido pelo vendedor

        $min--;
        $max--;
        $expression = '/^[A-Z0-9ÁÉÍÓ][a-z0-9- çáàãéêíóõô]{'.$min.','.$max.'}$/';

        if(preg_match($expression, $field)){
            return(true);
        }

        else{
            return(false);
        }
    }

    function checkPrice($field){ //Função para verificar o preço introduzido pelo vendedor

        $expressionPrice = '/^[1-9]{1}[0-9]*(?:\.[0-9]{1,2}){0,1}$/';
        // O número nunca pode começar por zero, só pode ter duas casas decimais e só pode ser positivo

        if(preg_match($expressionPrice, $field)){
            return(true);
        }

        else{
            return(false);
        }
    }
    
    function checkQuantity($field){ //Função para verificar a quantidade introduzida pelo vendedor

        $expressionQuantity = '/^[1-9]\d*$/'; // O número só pode ser inteiro e positivo

        if(preg_match($expressionQuantity, $field)){
            return(true);
        }

        else{
            return(false);
        }
    }

    function checkText($minText, $maxText, $field){ //Função para validar o conteúdo do elemento <textarea>

        
        $expression = '/^[\w-\"\', .çÁáàãÉéêÍíÓóõô]{'.$minText.','.$maxText.'}$/'; //Pode ser usada qualquer letra incluindo letras com acento

        if(preg_match($expression, $field)){
            return(true);
        }

        else{
            return(false);
        }
    }

    function checkSearch($field){ //Função para validar o conteúdo da pesquisa

        
        $expression = '/^[\w-\"\', .çÁáàãÉéêÍíÓóõô]*$/'; //Pode ser usada qualquer letra incluindo letras com acento

        if(preg_match($expression, $field)){
            return(true);
        }

        else{
            return(false);
        }
    }

    function checkClassification($minClass, $maxClass, $field){ 
    //Função para verificar o valor da classificação introduzido pelo utilizador ao comentar

        $expressionClassification = '/^['.$minClass.'-'.$maxClass.']{1}$/'; // O número só pode ir de 1 a 5 e só pode ter um digito

        if(preg_match($expressionClassification, $field)){
            return(true);
        }

        else{
            return(false);
        }
    }

    function checkDiscount($field){ 
    //Função para verificar a percentagem de desconto introduzida pelo vendedor
    
        if($field >= 0 && $field < 100){ // O número só pode estar entre 0 e 99
            return(true);
        }
    
        else{
            return(false);
        }
    }

?>