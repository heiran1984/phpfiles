<?php
require_once '../includes/DbOperations.php';

$response=array();

   if($_SERVER['REQUEST_METHOD']=='POST'){
      if(
          isset($_POST['username'])and
          isset($_POST['m_vam'])and
          isset($_POST['t_aghsat'])and
          isset($_POST['m_aghsat'])and
          isset($_POST['t_pardakhtshoda'])and
          isset($_POST['tozihat'])and
          isset($_POST['codes'])and
          isset($_POST['next_mah']))
      {
        $db=new DbOperations();
        $result=$db->newVam($_POST['username'],
                 $_POST['m_vam'],
                 $_POST['t_aghsat'],
                 $_POST['m_aghsat'],
                 $_POST['t_pardakhtshoda'],
                 $_POST['tozihat'],
                 $_POST['codes'],
                 $_POST['next_mah']
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
