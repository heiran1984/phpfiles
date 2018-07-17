<?php

require_once '../includes/DbOperations.php';
$response=array();

if($_SERVER['REQUEST_METHOD']=='POST'){
  if(
      isset($_POST['name'])and
      isset($_POST['value_mahiyane'])and
      isset($_POST['t_aghsat'])and
      isset($_POST['mizan']))
    {
      $db=new DbOperations();
      $result=$db->createSandogh(
                    $_POST['name'],
                    $_POST['value_mahiyane'],
                    $_POST['t_aghsat'],
                    $_POST['mizan']
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

 ?>
