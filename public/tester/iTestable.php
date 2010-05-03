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
      $unique_id_failed = array();
      $unique_id_passed = array();
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
        if(isset($vars[$test["param type"]]) && isset($vars[$test["param type"]][$test["param name"]]))
          $subject = $vars[$test["param type"]][$test["param name"]];
        else
          $subject = '';
        
        $passed = preg_match($test["pattern"], $subject);
        
        $test_passes = $passed == $test["should pass"];

        $unique_id = $test['unique id'];        
        if($unique_id != null)
        {
          if( $test_passes )
          {
            unset($unique_id_failed[$unique_id]);
            $unique_id_passed[$unique_id] = true;
          }
          else
          {
            if( isset($unique_id_passed[$unique_id]))
              $test_passes = true;
            else
              $unique_id_failed[$unique_id] = true;
          }
        }
        else if(! $test_passes)
          break;
      }
      if($test_passes)
      {
        $test_passes = count($unique_id_failed) == 0;
      }
      return $test_passes;
    }
      
    public function add_subtest($param_type, $param_name, $pattern, $should_pass = TRUE, $unique_id = NULL)
    {
      //validate input
      //TODO: VALIDATE INPUT
      
      // then ....
      $this->sub_tests[] = array( "param type" => $param_type,
                "param name" => $param_name, 
                "pattern" => $pattern,
                "should pass" => $should_pass,
                "unique id" => $unique_id);
      
    }
  }
?>