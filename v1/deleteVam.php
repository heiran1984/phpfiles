<?php
require_once '../includes/DbOperations.php';
    $response=array();
    $db=new DbOperations();
    $response=$db->deleteVam($_GET['string']);
    //echo json_encode($response);
?>
