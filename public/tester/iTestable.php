<?php
  interface iTestable
  {
    public function run_test($vars);
  }
  
  class RegExTester implements iTestable
  {
    private $sub_tests = array();
    
    public function run_test($vars)
    {
      $test_passes = true;
      foreach( $this->sub_tests as $test )
      {
        //vars is a hash of all testable variables
        //for instance, if i want to test the input field called "bob",
        //i'll say $vars["input"]["bob"]
        //vars looks like this: 
        //vars = {
        // input => {"bob" => "joefish", "password" => "secret"},
        // get => {},
        // ...
        //};
        $subject = $vars[$test["param type"]][$test["param name"]];
        
        $passed = preg_match($test["pattern"], $subject);
        
        $test_passes = $passed && $test["should pass"];
        
        if(! $test_passes)
          break;
      }
      return $test_passes;
    }
      
    public function addSubTest($param_type, $param_name, $pattern, $should_pass = TRUE)
    {
      //validate input
      //TODO: VALIDATE INPUT
      
      // then ....
      $this->sub_tests[] = array( "param type" => $param_type,
                "param name" => $param_name, 
                "pattern" => $pattern,
                "should pass" => $should_pass );
      
    }
  }
?>