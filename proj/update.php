<?php

$data = json_decode($_GET['models']);
$id = $data[0]->{'id'};
$cardNum = str_replace(" ", "", $data[0]->{'nam_card'});
$cardPerson = $data[0]->{'person'};
$cardDate = $data[0]->{'date'};
$cardCVV = $data[0]->{'cvv'};

function outError($message) {
    //header('HTTP/1.1 500 Internal Server Error');
    print($message);
    exit();
}

if (!empty($id) && !empty($cardNum) && !empty($cardPerson) && !empty($cardDate) && !empty($cardCVV)) {
    $count = substr_count($cardPerson, ' '); // 2


    if (strlen($cardNum) < 12 || $count < 1 || strlen($cardDate) != 9 || strlen($cardCVV) != 3 || strlen($cardPerson) > 20) {
        outError(" Ошибка1. Некорректно введены данные.");
    }
    $checkCardNum = preg_replace('~[^0-9]+~', '', $cardNum);
    $checkCardCVV = preg_replace('~[^0-9]+~', '', $cardCVV);
    $checkDate = explode(" / ", $cardDate);
    $checkDate0 = $checkDate[0];
    $checkDate1 = $checkDate[1];
    $checkDateNext0 = preg_replace('~[^0-9]+~', '', $checkDate0);
    $checkDateNext1 = preg_replace('~[^0-9]+~', '', $checkDate1);

    $checkPersonS = preg_replace('~^\w+ ^\w+~', '', $cardPerson);
    if ($checkCardNum != $cardNum || $checkCardCVV != $cardCVV || strlen($checkDate0) != 2 || strlen($checkDate1) != 4 || $checkDate0 != $checkDateNext0 || $checkDateNext1 != $checkDate1 || $checkPersonS != $cardPerson) {

        outError(" Ошибка2. Некорректно введены данные.");
//        $checkOrderNum != $orderNum ? "истина" : "ложина" 
    }
    //echo "Проверка введенных данных завершена. ";
} else {
    outError("Ошибка. Недостаточно данных.");
}

$mysqli = new mysqli('localhost', 'ck43709_temp', 'Z0Aooywo','ck43709_temp');
/* проверка соединения */
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}

if ($resultSelect = $mysqli->query("SELECT * FROM cardTabl WHERE nam_card = '$cardNum';")) {
    $rowSel = $resultSelect->fetch_assoc();
    if (empty($rowSel) || $rowSel['id'] == $id) {
        if ($result = $mysqli->query("UPDATE cardTabl SET 
            nam_card = '$cardNum',
            person = '$cardPerson',
            date = '$cardDate',
            cvv = '$cardCVV'
            WHERE id='$id'")) {
				echo json_encode(new stdClass);
        }else{
			outError("Ошибка. Неудачное обновление.");
		}
    } else {
        outError("Ошибка. Указанный номер карты уже есть в базе, необходимо ввести другой или редактировать имеющийся.");
    }
} else {
    outError('Select запрос не удался: ' . mysql_error() . '<br/>');
}


mysqli_close($mysqli);

