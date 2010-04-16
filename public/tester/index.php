<?php 
session_start();
#connect to database

#TODO: READ FROM database.conf file.
$db = new mysqli('localhost','root','','holey_moley_dev'); 
$error = '';
$body = '';

$key = $_SERVER["REQUEST_URI"];
#expects a 10 digit key. /^([a-z]{10,10})(.*)/
if(preg_match("/([a-z]{10,10})\/(.*)/", $key, $matches))
{
  $key = $matches[1];
  $url = $matches[2];
}
else
  $error .= "No key found.";

# get suite, tests from database
# NO IDEA WHY THIS DOESNT WORK!!!!
# if( $stmt = $db->prepare("SELECT tests.id FROM suites, tests WHERE tests.suite_id = suites.id AND suites.key = ?") ) {
#   if(! $stmt->bind_param('s',$key ) );
#   {
#     $error .= "Error binding parameter";    
#   }
#   if(!$stmt->execute()){
#     $error .= "Database error.";
#   }
# 
#   if(!$stmt->bind_result($test))
#     $error .= "Error binding result";
# 
#   while($result = $stmt->fetch()){
#     if($result == false)
#       $error .= "error fetching";
#       
#     $body .= $test."<br />";
#   }
#   $stmt->close();
# }
# else
#   $error .= "Error preparing statement";


$suite_id = '';

$query = "SELECT suites.id FROM suites WHERE suites.key = '$key';";
if($result = $db->query($query)) {
  while($row = $result->fetch_row())
    foreach ($row as $value)
      $suite_id = $value;
      
    $tests = array();

    $query = "SELECT tests.id FROM tests WHERE tests.suite_id = $suite_id;";
    if($result = $db->query($query)) {
      while($row = $result->fetch_row())
        foreach ($row as $value)
          $tests[] = $value;


        # 
        # for each test, require tests/test.php which includes the FSM
        # ... realistically, we should merge our FSMs for each test... but maybe we won't do that for this project...
        # once we have the state machine loaded...
        #now, just include first test...
        # require("tests/$tests[0].php");
          
      if( isset($url) )
        require ("tests/22.php"); #this is for directory traversal...
          
    }
    else
      $error .= "<br/>backend error retrieving suite :(";

}
else
  $error .= "<br/>invalid key ($key):(";#$query;



$db->close();







$title = "generic title";
if(strlen($error) > 0)
  $body = $error;
else if(strlen($body) == 0)
  $body = "generic body";
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