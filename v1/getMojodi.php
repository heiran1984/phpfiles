<?php
require_once '../includes/DbOperations.php';
//if($_GET['code']){

    $response=array();
    $db=new DbOperations();

    $response=$db->getMojodi();
    $response['tozv']=$db->tuser("users");
    $response['twam']=$db->tuser("wam");
    $response['tarikh']=date("Y-m-d");
    $response['mozv']=$db->MojodiOzv();
    echo json_encode($response);

?>
