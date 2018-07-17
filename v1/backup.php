<?php
$dbhost='localhost';
$dbuser= 'root';
$dbpass='';
$dbname='Sandoogh';
$date_string= date("Ymd");
$mySQLDir='"C:\xampp\mysql\bin\"';

$cmd ="mysqldump  -h {$dbhost} -u {$dbuser} -p {$dbpass} {$dbname}  > {$date_string}_{$dbname}.sql";
exec($cmd);
 ?>
