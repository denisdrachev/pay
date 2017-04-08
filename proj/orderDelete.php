<?php

$data = json_decode($_GET['models']);
 $id = $data[0]->{'id'};
 
function outError($message){
    header('HTTP/1.1 500 Internal Server Error');
    print($message);
    exit();
}
$mysqli = new mysqli('localhost', 'root', 'root','pay');
/* проверка соединения */
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}
if ($result = $mysqli->query("DELETE FROM orderTabl WHERE id=".$id.";")) {
    echo "Успешно удалено";
}else{
    echo "Ошибка удаления. Возможно указан несуществующий заказ";
}
mysqli_close($mysqli); 

