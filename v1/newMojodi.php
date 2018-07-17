<?php

require_once '../includes/DbOperations.php';
$response=array();

if ($_GET['string']=="new") {
if($_SERVER['REQUEST_METHOD']=='POST'){

  if(
      isset($_POST['id'])and
      isset($_POST['mablagh'])and
      isset($_POST['mah']))
    {
      $db=new DbOperations();
      $result=$db->AfzayesheMojodi(
                    $_POST['id'],
                    $_POST['mablagh'],
                    $_POST['mah']
                  );
      if($result==1){
          $response['error']=false;
          $response['message']="User registered successfully";
      }elseif($result==2){
          $response['error']=true;
          $response['message']="some error occured please try again";
    }elseif ($result==0) {
          $response['error']=true;
          $response['message']="It seems you are already registered, please choose a diffrent email and username";
    }
    }else{
      $response['error']=true;
      $response['message']="Require fields are missing";
    }
    }else{
      $response['error']=true;
      $response['message']="Invalid Request";
}

echo json_encode($response);
}

else {
  $db=new DbOperations();
  $response=$db->getnewmojodi($_GET['string']);
  echo json_encode($response);
  
}

 ?>
