<?php
require_once('state.php');
require_once('itestable.php');

class Transition {
  private $name, $source, $destination, $testable;
  
  public function __construct($name, $source, $destination, $testable)
  {
    $this->name = $name;
    if(is_a($source, "State") )
      $this->source = $source;
    else
      throw new Exception("Source must be object of class State");

    if(is_a($destination, "State") )
      $this->destination = $destination;
    else
      throw new Exception("Destination must be object of class State");

    if(is_a($testable, "iTestable") )
      $this->testable = $testable;
    else
      throw new Exception("Testable must be object of class iTestable");
  }
  
  public function can_transition($current_state)
  {
    // //check source state vs source for this transition
    // if(! $current_state->is_equal_to($this->source))
    //   return false;
      
    //generate vars array
    $vars = array();
    //add $_GET
    $vars['GET'] = $_GET;
    //add $_POST
    $vars['POST'] = $_POST;
    
    //add $_url
    $key = $_SERVER["REQUEST_URI"];
    if(preg_match("/([a-z]{10,10})(\/(.*))?/", $key, $matches))
      if(isset($matches[2]))
        $url = $matches[2];
      else
        $url = "";
    else
      throw new Exception("Somehow the url is completely invalid!  yay!");
    
    $vars['STATIC'] = array('URL' => $url);
        
    return $this->testable->run_test($vars);
  }
  
  public function source()
  {
    return $this->source;
  }
  
  public function destination()
  {
    return $this->destination;
  }
  
  public function name()
  {
    return $this->name;
  }
}
?>