<?php
require 'FSM.php';
 
//connect to database

//TODO: READ FROM database.conf file.
$db = new mysqli(‘localhost’,‘root’,‘’,‘database’); 


$key = $_SERVER["REQUEST_URI"];
if(preg_match("/([a-z]{10,10})\/?$/", $key, $matches))
  $key = $matches[0];
else
  $key = -1;
  
//get suite, tests from database

/*
$stmt = $db->prepare("SELECT test.id FROM suite, test WHERE test.id=suite.id AND suite.key=?"));
$stmt->bind_param("s",$key);


//for each test, require tests/test.php which includes the FSM
//... realistically, we should merge our FSMs for each test... but maybe we won't do that for this project...
//once we have the state machine loaded...
*/
$title = "generic title";
$body = $key;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
  	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  	<title><?= $title ?></title>
  </head>
  <body><?= $body ?></body>
</html>