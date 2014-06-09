<?php

session_start();

$userData = '';
if ( isset($_SESSION['ESLIP_USER_DATA']) ){
    $userData = $_SESSION['ESLIP_USER_DATA'];
}

$data = array(
    'user' => $userData
);


echo json_encode($data);

exit;

?>