<?php

$data = json_decode($_GET['models']);
$id = $data[0]->{'id'};
$orderNum = $data[0]->{'nam_ord'};
$orderCost = str_replace(" ", "", $data[0]->{'cost'});
$orderCurr = $data[0]->{'currency'};
$card_id = $data[0]->{'card_id'};

function outError($message) {
    //header('HTTP/1.1 500 Internal Server Error', true, 500);
    print($message);
    exit();
}

if (!empty($id) && !empty($orderNum) && !empty($orderCost) && !empty($orderCurr)) {

    if ($orderNum <= 0 || strlen($orderNum) > 10 || $orderCost <= 0 || strlen($orderCost) > 20 || $orderCurr != "RUB" && $orderCurr != "USD") {
        outError(" Ошибка1. Некорректно введены данные.");
    }
    $checkOrderNum = preg_replace('~[^0-9]+~', '', $orderNum);
    $checkCardId = preg_replace('~[^0-9]+~', '', $card_id);
    $checkOrderCost = preg_replace('~{LNUM}[\.][0-9]*~', '', $orderCost);
    if ($checkOrderNum != $orderNum || $checkOrderCost != $orderCost || $checkCardId != $card_id) {
        outError(" Ошибка2. Некорректно введены данные.");
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



$resultSelectCardID = $mysqli->query("SELECT * FROM orderTabl WHERE id = '$id';");
$rowCardID = $resultSelectCardID->fetch_assoc();

if ($resultSelect = $mysqli->query("SELECT * FROM orderTabl WHERE nam_ord = '$orderNum';")) {
    $row = $resultSelect->fetch_assoc();
    if (!empty($rowCardID)){
        if (empty($row) || $row['id'] == $id) {
            //        $hashDB = hash('sha256',$cardCVV);
            if ($result = $mysqli->query("UPDATE orderTabl SET 
                nam_ord = '$orderNum',
                cost = '$orderCost',
                currency = '$orderCurr',
                card_id = '$card_id'
                WHERE id='$id';")) {
                //echo "Успешно обновлено";
				echo json_encode(new stdClass);
            } else {
                outError ("Некорректный ID записи");
            }
        } else {
            outError("Ошибка. Указанный номер заказа уже есть в базе, необходимо ввести другой.");
        }
    }else{
            outError("Ошибка. Указанный [id карты] не найден в базе.");
    }
} else {
    outError('Select запрос не удался: ' . mysql_error() . '<br/>');
}


mysqli_close($mysqli);
exit;