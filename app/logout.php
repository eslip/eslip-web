<?php

session_start();

unset($_SESSION['ESLIP_USER_DATA']);

session_destroy();

$result = "SUCCESS";

$data = array(
    "status" => $result
);

echo json_encode($data);

exit;

?>