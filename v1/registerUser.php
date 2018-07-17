<?php

require_once '../includes/DbOperations.php';
$response=array();

if($_SERVER['REQUEST_METHOD']=='POST'){
  if(
      isset($_POST['username'])and
      isset($_POST['mojodi'])and
      isset($_POST['password']))

    {
      $db=new DbOperations();
      $result=$db->createUser(
                    $_POST['username'],
                    $_POST['password'],
                    $_POST['mojodi'],$_GET['string']
                  );
      if($result==1){
          $response['error']=false;
          $response['message']="عملیات با موفقیت انجام شد";
      }elseif($result==2){
          $response['error']=true;
          $response['message']="some error occured please try again";
      }elseif ($result==0) {
          $response['error']=true;
          $response['message']="کاربری با این نام وجود دارد";
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
