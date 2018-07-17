<?php
   class DbConnect
   {
     private $con;

     function __construct()
     {
       # code...
     }

     function connect()
     {
       include_once (dirname(__FILE__).'/constans.php');
       try {
         $this->con=new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER,DB_PASSWORD);
         $this->con->exec('set names utf8');
       } catch (PDOException $e) {
         echo $e->getMessage();
         print_r($connection->errorInfo());
       }

       return $this->con;
     }
   }
 ?>
