<?php

require_once(dirname(dirname(__FILE__)) . '/config/config.php');

ini_set('display_errors',1); 
 error_reporting(E_ALL);

//mysql_select_db($dbname,$con);

if (!$db)
 {
 die('Could not connect: ');
 }

?>