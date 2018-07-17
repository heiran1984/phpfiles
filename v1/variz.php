<?php
require_once '../includes/DbOperations.php';

$response=array();

   if($_SERVER['REQUEST_METHOD']=='POST'){
      if(
          isset($_POST['id'])and
          isset($_POST['mablagh']))
      {
        $db=new DbOperations();
        $result=$db->Variz($_POST['id'],
                 $_POST['mablagh']
                );
      }
    if($result==1) {
      $response['error']=false;
      $response['message']="User registered successfully";
    }else{
      $response['error']=true;
      $response['message']="error";
    }
  }

    echo json_encode($response);

?>
