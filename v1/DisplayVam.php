<?php
require_once '../includes/DbOperations.php';
//if($_GET['code']){


    $response=array();
    $db=new DbOperations();

    $response=$db->displayVam($_GET['string']);
    echo json_encode($response);

?>
