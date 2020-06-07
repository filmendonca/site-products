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

    function CheckProductName($min, $max, $field){ // Fun��o para verificar o nome do produto introduzido pelo vendedor

        $min--;
        $max--;
        $expression = '/^[A-Z0-9����][a-z0-9- ����������]{'.$min.','.$max.'}$/';

        if(preg_match($expression, $field)){
            return(true);
        }

        else{
            return(false);
        }
    }

    function checkPrice($field){ //Fun��o para verificar o pre�o introduzido pelo vendedor

        $expressionPrice = '/^[1-9]{1}[0-9]*(?:\.[0-9]{1,2}){0,1}$/';
        // O n�mero nunca pode come�ar por zero, s� pode ter duas casas decimais e s� pode ser positivo

        if(preg_match($expressionPrice, $field)){
            return(true);
        }

        else{
            return(false);
        }
    }
    
    function checkQuantity($field){ //Fun��o para verificar a quantidade introduzida pelo vendedor

        $expressionQuantity = '/^[1-9]\d*$/'; // O n�mero s� pode ser inteiro e positivo

        if(preg_match($expressionQuantity, $field)){
            return(true);
        }

        else{
            return(false);
        }
    }

    function checkText($minText, $maxText, $field){ //Fun��o para validar o conte�do do elemento <textarea>

        
        $expression = '/^[\w-\"\', .��������������]{'.$minText.','.$maxText.'}$/'; //Pode ser usada qualquer letra incluindo letras com acento

        if(preg_match($expression, $field)){
            return(true);
        }

        else{
            return(false);
        }
    }

    function checkSearch($field){ //Fun��o para validar o conte�do da pesquisa

        
        $expression = '/^[\w-\"\', .��������������]*$/'; //Pode ser usada qualquer letra incluindo letras com acento

        if(preg_match($expression, $field)){
            return(true);
        }

        else{
            return(false);
        }
    }

    function checkClassification($minClass, $maxClass, $field){ 
    //Fun��o para verificar o valor da classifica��o introduzido pelo utilizador ao comentar

        $expressionClassification = '/^['.$minClass.'-'.$maxClass.']{1}$/'; // O n�mero s� pode ir de 1 a 5 e s� pode ter um digito

        if(preg_match($expressionClassification, $field)){
            return(true);
        }

        else{
            return(false);
        }
    }

    function checkDiscount($field){ 
    //Fun��o para verificar a percentagem de desconto introduzida pelo vendedor
    
        if($field >= 0 && $field < 100){ // O n�mero s� pode estar entre 0 e 99
            return(true);
        }
    
        else{
            return(false);
        }
    }

?>