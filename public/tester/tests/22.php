<?php
  //create states
  //states are...
  //start
  //simple end
  //fancy end
  require_once('test.php');
  
  $start_state = new State("start",false, "..<br>this is a fake file structure<br>fer serious<br/>");
  $simple_end = new State("simple end", true, ".. you have violated this system's security policy.  oh noes! <br/>");
  $fancy_end = new State("fancy end",true,".. very clever.  actually not that clever. <br/>");
  
  $simple_pattern = "/\.{2,2}/";
  $fancy_pattern = "/(%2e){2,2}/";

  // $anything_pattern = "/.*/";
  
  $do_nothing_test = new RegExTester();
  $do_nothing_test->add_subtest('STATIC', 'URL', $simple_pattern, FALSE);
  $do_nothing_test->add_subtest('STATIC', 'URL', $fancy_pattern, FALSE);

  $simple_test = new RegExTester();
  $simple_test->add_subtest('STATIC', 'URL', $simple_pattern);
  $simple_test->add_subtest('STATIC', 'URL', $fancy_pattern, FALSE);
  
  $stay_simple_test = new RegExTester();
  $stay_simple_test->add_subtest('STATIC', 'URL', $fancy_pattern, FALSE);
  
  $fancy_test = new RegExTester();
  $fancy_test->add_subtest('STATIC', 'URL', $fancy_pattern);

  $stay_fancy_test = new RegExTester();
  // $stay_fancy_test->add_subtest('STATIC', 'URL', $anything_pattern);

  $do_nothing = new Transition("DO NOTHING", $start_state, $start_state, $do_nothing_test);
  $get_simple = new Transition("UP ONE LEVEL WITH STANDARD CHARS", $start_state, $simple_end, $simple_test);
  $get_fancy = new Transition("UP ONE LEVEL WITH SPECIAL CHARS", $start_state, $fancy_end, $fancy_test);
  
  $stay_simple = new Transition("STILL SIMPLY DONE", $simple_end, $simple_end, $stay_simple_test);
  $get_fancier = new Transition("GET FANCIER", $simple_end, $fancy_end, $fancy_test);
  
  $stay_fancy = new Transition("STILL FANCILY DONE", $fancy_end, $fancy_end, $stay_fancy_test);
  
  
  $test_22 = new Test( array( $do_nothing, $get_simple, $get_fancy, 
                                $stay_simple, $get_fancier,
                                $stay_fancy),
                                $start_state );
?>