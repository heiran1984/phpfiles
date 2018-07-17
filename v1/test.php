<?php
require_once '../includes/jdateTime.class.php';
//echo jDateTime::date('l j F Y', false, false, true, 'Asia/Tehran');
$date = new jDateTime(true, true, 'Asia/Tehran');
echo $date->date("l j F Y H:i");


?>
