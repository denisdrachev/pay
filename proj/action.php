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
        
    $checkPersonS = preg_replace('~^\w+ ^\w+~','',$cardPerson);
    
    $checkOrderCost = preg_replace('~{LNUM}[\.][0-9]*~','', $orderCost); 
    
    if ($checkOrderNum != $orderNum || $checkCardNum != $cardNum || $checkCardCVV != $cardCVV || strlen($checkDate0) != 2 || strlen($checkDate1) != 4 || $checkDate0 != $checkDateNext0 || $checkDateNext1 != $checkDate1 || $checkOrderCost != $orderCost || $checkPersonS != $cardPerson){
        
        outError(" Ошибка2. Некорректно введены данные.");
    }
        echo "Проверка введенных данных завершена. ";
    }else{
        outError("Ошибка. Введены не все необходимые данные!");
    }
	
	$mysqli = new mysqli('localhost', 'ck43709_temp', 'Z0Aooywo','ck43709_temp');
/* проверка соединения */
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}

    if (!$result = $mysqli->query("SELECT * FROM cardTabl WHERE nam_card = '$cardNum';"))
		outError('Первый запрос не удался: ' . mysql_error());
    $row = $result->fetch_assoc();

    if (empty($row)){
//        $hashDB = hash('sha256',$cardCVV);

        if (!$resultCard = $mysqli->query("INSERT INTO cardTabl (nam_card,person,date,cvv) VALUES('$cardNum','$cardPerson','$cardDate','$cardCVV');"))
			outError('Второй запрос не удался: ' . mysql_error());
        $idCard = $resultCard;
        
    }else{
		$idCard = $row['id'];
        //echo 'Эта карта уже в базе есть. ID = '.$idCard.'<br/>';
    }
    
    if (!$result = $mysqli->query("SELECT * FROM orderTabl WHERE nam_ord = '$orderNum';"))
		outError('Третий запрос не удался: ' . mysql_error());
    $row = $result->fetch_assoc();
    
    if (empty($row)){
        if (!$resultOrder = $mysqli->query("INSERT INTO orderTabl (nam_ord,cost,currency,card_id) VALUES('$orderNum','$orderCost','$orderCurr','$idCard');"))
			outError('Четвертый запрос не удался: ' . mysql_error());
        echo "Данные успешно внесены в базу данных.";
    }else{
        outError("Ошибка. Указанный номер заказа уже есть в базе, необходимо ввести другой.");
    }
    
mysqli_close($mysqli);