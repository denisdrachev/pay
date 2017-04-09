<?php

$data = json_decode($_GET['models']);
$orderNum = $data[0]->{'nam_ord'};
$orderCost = str_replace(" ", "", $data[0]->{'cost'});
$orderCurr = $data[0]->{'currency'};
$card_id = $data[0]->{'card_id'};

function outError($message) {
    //header('HTTP/1.1 500 Internal Server Error');
    print($message);
    exit();
}

if (!empty($orderNum) && !empty($orderCost) && !empty($orderCurr)) {

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
    outError("Ошибка. Введены не все необходимые данные!");
}

$mysqli = new mysqli('localhost', 'ck43709_temp', 'Z0Aooywo','ck43709_temp');
/* проверка соединения */
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}

if ($resultSelect = $mysqli->query("SELECT * FROM orderTabl WHERE nam_ord = '$orderNum';")) {
    $row = $resultSelect->fetch_assoc();
    if (empty($row)) {
        if ($resultSelectID = $mysqli->query("SELECT * FROM orderTabl WHERE id = '$card_id';")) {
            $rowID = $resultSelectID->fetch_assoc();
            if (!empty($rowID)) {
                if ($result = $mysqli->query("INSERT INTO orderTabl (nam_ord,cost,currency,card_id) VALUES('$orderNum','$orderCost','$orderCurr','$card_id');")) {
                    //echo "Успешно добавлено";
					echo json_encode(new stdClass);
                }
            } else {
                outError("Ошибка. Указанный ID карты отсутсвует в базе.");
            }
        } else {
            outError('Select запрос не удался: ' . mysql_error() . '<br/>');
        }
    } else {
        outError("Ошибка. Указанный номер заказа уже есть в базе, необходимо ввести другой.");
    }
} else {
    outError('Select запрос не удался: ' . mysql_error() . '<br/>');
}

mysqli_close($mysqli);
