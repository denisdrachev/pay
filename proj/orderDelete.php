<?php

$data = json_decode($_GET['models']);
 $id = $data[0]->{'id'};
 
function outError($message){
    header('HTTP/1.1 500 Internal Server Error');
    print($message);
    exit();
}
if (empty($id)){
	outError("Ошибка. Не указан ID.");
}

$mysqli = new mysqli('localhost', 'ck43709_temp', 'Z0Aooywo','ck43709_temp');
/* проверка соединения */
if ($mysqli->connect_errno) {
    outError("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}
if ($result = $mysqli->query("DELETE FROM orderTabl WHERE id='$id';")) {
    echo json_encode(new stdClass);
}else{
    outError ("Ошибка удаления.");
}
mysqli_close($mysqli); 

