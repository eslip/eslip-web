<?php

session_start();

$userData = '';
if ( isset($_SESSION['ESLIP']) ){
    $eslipData = $_SESSION['ESLIP'];
}

$data = array(
    'eslip' => $eslipData
);


echo json_encode($data);

exit;

?>