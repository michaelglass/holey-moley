<?php
$last_transition = 'NULL'; //default
$query = "SELECT transition_name FROM histories WHERE histories.suite_id = '$suite_id' ORDER BY created_at desc LIMIT 1;";
$error .= "[$query]";
if($result = $db->query($query)) {
  if(mysqli_num_rows($result) == 1 && $row = $result->fetch_row())
    $last_transition = $row[0];
  else
    $error .= "<br/>no history yet";
  $error .= mysqli_num_rows($result);
}
else
  $error .= "<br/>backend error :(";

#all transitions and their destinations
$transitions = array( "NULL"=>"start",
                      "DO NOTHING"=>"start",
                      "UP ONE LEVEL WITH STANDARD CHARS"=>"simple end",
                      "STILL SIMPLY DONE"=>"simple end",
                      "GETTING FANCIER" =>"fancy end",
                      "UP ONE LEVEL WITH SPECIAL CHARS"=>"fancy end",
                      "STILL FANCILY DONE"=>"fancy end");
#whether transitions lead to final positions or not
$finals      = array( "NULL"=>FALSE,
                      "DO NOTHING"=>FALSE,
                      "UP ONE LEVEL WITH STANDARD CHARS"=>TRUE,
                      "STILL SIMPLY DONE"=>TRUE,
                      "GETTING FANCIER" =>TRUE,
                      "UP ONE LEVEL WITH SPECIAL CHARS"=>TRUE,
                      "STILL FANCILY DONE"=>TRUE);                      

$state = $transitions[$last_transition];
$error .= "<br/>$state";
// transition completely based on url for directory traversal...
switch($state)
{
  case 'start':
    error_log("state is start");
    if( preg_match("/\.{2,2}/", $url) )
    {    
      $transition = "UP ONE LEVEL WITH STANDARD CHARS";
      $body = "..<br>this is a fake file structure<br>fer serious<br>";
    }
    else if (preg_match("/(%2e|\.){2,2}/", $url) )
    {
      $transition = "UP ONE LEVEL WITH SPECIAL CHARS";
      $body = "..<br>this is a fake file structure<br>fer serious<br>";
    }
    else
      $transition = "DO NOTHING";
    break;

  case 'simple end':
  error_log("state is simple_end");
      if(preg_match("/(%2e|\.){2,2}/", $url))
      {
        $transition = "UP ONE LEVEL WITH SPECIAL CHARS";
        $body = "..<br>this is a fake file structure<br>fer serious<br>";
      }      
      else
      {
        $transition = "STILL SIMPLY DONE";
        $body = "..<br>this is a fake file structure<br>fer serious<br>";
      }
      break;
  case 'fancy end':
    error_log("state is fancy end");  
    $transition = "STILL FANCILY DONE";
    $body = "..<br>this is a fake file structure<br>fer serious<br>";
}

$query = "INSERT INTO histories (suite_id, transition_name, final, created_at, updated_at) VALUES($suite_id, '$transition', ".($finals[$transition] ? 'TRUE' : 'FALSE').", NOW(), NOW() );";
if(! $db->query($query))
  $error .= "<br/>backend error updating history :(";


 
?>