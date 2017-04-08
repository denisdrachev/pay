<?php

 $orderNum = $_POST['orderNum'];
 $orderCost = str_replace(" ","",$_POST['orderCost']);
 $orderCurr = $_POST['orderCurr'];
 $cardNum = str_replace(" ","",$_POST['cardNum']);
 $cardPerson = $_POST['cardPerson'];
 $cardDate = $_POST['cardDate'];
 $cardCVV = $_POST['cardCVV'];

function outError($message){
    header('HTTP/1.1 500 Internal Server Error');
    print($message);
    exit(1);
}

//function check(){
    
    if (!empty($orderNum) && !empty($orderCost) && !empty($orderCurr) && !empty($cardNum) && !empty($cardPerson) && !empty($cardDate) && !empty($cardCVV)){
    
    $count = substr_count($cardPerson, ' '); // 2
        

    if ($orderNum <= 0 || strlen($orderNum) > 10 || $orderCost <= 0 || strlen($orderCost) > 20 || strlen($cardNum) < 12 || $count < 1 || strlen($cardDate) != 9 || strlen($cardCVV) != 3 || $orderCurr != "RUB" && $orderCurr != "USD" || strlen($cardPerson) > 20){
        outError(" Ошибка1. Некорректно введены данные.");
    }
        $patternInt = "~[^0-9]+~";
    
    $checkOrderNum = preg_replace('~[^0-9]+~','',$orderNum); 
    $checkCardNum = preg_replace('~[^0-9]+~','', $cardNum); 
    $checkCardCVV = preg_replace('~[^0-9]+~','', $cardCVV); 
    $checkDate = explode(" / ", $cardDate);
    $checkDate0 = $checkDate[0];
    $checkDate1 = $checkDate[1];
    $checkDateNext0 = preg_replace('~[^0-9]+~','', $checkDate0); 
    $checkDateNext1 = preg_replace('~[^0-9]+~','', $checkDate1); 
        
//    $checkPerson0 = preg_replace('\w+.*','',$checkDate0);
//    $checkPerson1 = preg_replace('\w+.*','',$checkDate1);
    $checkPersonS = preg_replace('~^\w+ ^\w+~','',$cardPerson);
    
    $checkOrderCost = preg_replace('~{LNUM}[\.][0-9]*~','', $orderCost); 
    
    if ($checkOrderNum != $orderNum || $checkCardNum != $cardNum || $checkCardCVV != $cardCVV || strlen($checkDate0) != 2 || strlen($checkDate1) != 4 || $checkDate0 != $checkDateNext0 || $checkDateNext1 != $checkDate1 || $checkOrderCost != $orderCost || $checkPersonS != $cardPerson){
        
        outError(" Ошибка2. Некорректно введены данные.");
//        $checkOrderNum != $orderNum ? "истина" : "ложина" 
    }
        echo "Проверка введенных данных завершена. ";
    }else{
        outError("Ошибка. Введены не все необходимые данные!");
    }
//}

//    $count = substr_count($cardPerson, ' '); // 2
//if ($orderNum <= 0 || $orderCost <= 0 || strlen($cardNum) < 16 || $count < 1 || strlen($cardDate) != 9 || strlen($cardCVV) != 3 || $orderCurr != "RUB" && $orderCurr != "USD"){
//    outError($orderCurr." Ошибка. Некорректно введены данные.");
//}




    $link = mysql_connect('localhost', 'root', 'root')
    or outError('Не удалось соединиться: ' . mysql_error());
//    echo 'Соединение успешно установлено'.'<br/>';
    mysql_select_db('pay') or outError('Не удалось выбрать базу данных');
    
    $querySelect = "SELECT * FROM cardTabl WHERE nam_card = "."'".$cardNum."'".";";
//    echo $querySelect.'<br/>';
    $result = mysql_query($querySelect) or outError('Первый запрос не удался: ' . mysql_error().'<br/>');
    $row = mysql_fetch_array($result);
    $idCard = $row[0];
    if (empty($row[0])){
//        $hashDB = hash('sha256',$cardCVV);
        $queryCard = "INSERT INTO cardTabl (nam_card,person,date,cvv) VALUES("."'".$cardNum."'".","."'".$cardPerson."'".","."'".$cardDate."'".",".$cardCVV.");";
        $resultCard = mysql_query($queryCard) or outError('Второй запрос не удался: ' . mysql_error().'<br/>');
        $idCard = $resultCard;
//        echo $resultCard.'<br/>';
        
    }else{
//        echo 'Эта карта уже в базе есть. ID = '.$idCard.'<br/>';
    }
    
    $querySelectOrder = "SELECT * FROM orderTabl WHERE nam_ord = ".$orderNum.";";
//    echo $querySelectOrder.'<br/>';
    $result = mysql_query($querySelectOrder) or outError('Третий запрос не удался: ' . mysql_error().'<br/>');
    $row = mysql_fetch_array($result);
    
    if (empty($row[0])){
        $queryOrder = "INSERT INTO orderTabl (nam_ord,cost,currency,card_id) VALUES("."'".$orderNum."'".",".$orderCost.","."'".$orderCurr."'".",".$idCard.");";
//        echo "<br/><br/><br/>".$queryOrder.'<br/>';
        $resultOrder = mysql_query($queryOrder) or outError('Четвертый запрос не удался: ' . mysql_error().'<br/>');
        
        echo "Данные успешно внесены в базу данных.";
//        echo "Результат третьего запроса: ".$resultOrder."<br/>";
        
    }else{
        outError("Ошибка. Указанный номер заказа уже есть в базе, необходимо ввести другой.");
    }
    
    
    
//    echo $row[0].'<br/>';
    
//    $queryCard = "INSERT INTO cardTabl (nam_card,person,date,cvv) VALUES("."'".$cardNum."'".","."'".$cardPerson."'".","."'".$cardDate."'".",".$cardCVV.");";
//        $resultCard = mysql_query($queryCard) or die('Второй запрос не удался: ' . mysql_error().'<br/>');
//    echo $resultCard.'<br/>';
    
//    $querySelect = 'SELECT id FROM cardTabl WHERE nam_card = '.$cardNum.';'
//    
//    $queryOrder = 'INSERT INTO orderTabl (nam_ord,cost,currency,card_id) VALUES(15,col1*2);';
    
    
    mysql_close($link);