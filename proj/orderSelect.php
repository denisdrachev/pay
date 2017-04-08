<?php

function outError($message){
    header('HTTP/1.1 500 Internal Server Error');
    print($message);
    exit(1);
}
$mysqli = new mysqli('localhost', 'root', 'root','pay');
/* проверка соединения */
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}
if ($result = $mysqli->query("SELECT * FROM orderTabl;")) {
    while ($row = $result->fetch_object()){
        $user_arr[] = $row;
    }
    echo json_encode($user_arr);
    /* очищаем результирующий набор */
    $result->close();
}
return;
