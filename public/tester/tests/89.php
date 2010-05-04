<?php
require_once('test.php');

$form = new Form("login.php");
$form->add_input('username');
$form->add_input('password');

$start_state = new state("start", false, "Login to FakeCo!<br/>", NULL, $form, "login");
$failed_login_state = new state("failed", false, "Login Failed, please try again<br/>Login to FakeCo!<br/>", NULL, $form, "login");

$blind_state = new state("blind", true, "SYNTAX ERROR.  HERE IS ALL OF OUR IMPORTANT INFORMATION:<br/>FAVORITE COLOR: RED<br/>", NULL, $form, "blind attack succeeded"  );
$simple_state = new state("simple", true, "you have logged in as bob<br/>", NULL, $form, "simple attack successful");
$fancy_state = new state("fancy", true, "you have logged in as SUPERBOB (you are 1337)<br/>", NULL, $form, "fancy attack successful");

$blind_pattern = "/\'|\"|\)/";
$simple_pattern = "/\w*'\s*(--|#|or)/i";
$fancy_pattern = "/(\/\*.*(\/\*)?)|(;|union)|(0x|char)/i";

$do_absolutely_nothing_pattern = "/login\.php/i";

// $anything_pattern = "/.*/";


$do_absolutely_nothing_test = new RegExTester();
$do_absolutely_nothing_test->add_subtest('STATIC', 'URL', $do_absolutely_nothing_pattern, false);

$do_absolutely_nothing_test->add_subtest('POST', 'username', $blind_pattern, false);
$do_absolutely_nothing_test->add_subtest('POST', 'username', $simple_pattern, false);
$do_absolutely_nothing_test->add_subtest('POST', 'username', $fancy_pattern, false);
$do_absolutely_nothing_test->add_subtest('POST', 'password', $blind_pattern, false); #perhaps we want to allow for [] of fields to check over...
$do_absolutely_nothing_test->add_subtest('POST', 'password', $simple_pattern, false);
$do_absolutely_nothing_test->add_subtest('POST', 'password', $fancy_pattern, false);

$do_nothing_test = new RegExTester();
$do_nothing_test->add_subtest('STATIC', 'URL', $do_absolutely_nothing_pattern);
$do_nothing_test->add_subtest('POST', 'username', $blind_pattern, false);
$do_nothing_test->add_subtest('POST', 'username', $simple_pattern, false);
$do_nothing_test->add_subtest('POST', 'username', $fancy_pattern, false);
$do_nothing_test->add_subtest('POST', 'password', $blind_pattern, false); #perhaps we want to allow for [] of fields to check over...
$do_nothing_test->add_subtest('POST', 'password', $simple_pattern, false);
$do_nothing_test->add_subtest('POST', 'password', $fancy_pattern, false);

$blind_test = new RegExTester();
$blind_test->add_subtest('STATIC', 'URL', $do_absolutely_nothing_pattern);
$blind_test->add_subtest('POST', 'username', $blind_pattern, true, "UNIQUE_BLIND");
$blind_test->add_subtest('POST', 'username', $simple_pattern, false);
$blind_test->add_subtest('POST', 'username', $fancy_pattern, false);
$blind_test->add_subtest('POST', 'password', $blind_pattern, true, "UNIQUE_BLIND");
$blind_test->add_subtest('POST', 'password', $simple_pattern, false);
$blind_test->add_subtest('POST', 'password', $fancy_pattern, false);

$stay_blind_test = new RegExTester();
$stay_blind_test->add_subtest('POST', 'username', $simple_pattern, false);
$stay_blind_test->add_subtest('POST', 'username', $fancy_pattern, false);
$stay_blind_test->add_subtest('POST', 'password', $simple_pattern, false);
$stay_blind_test->add_subtest('POST', 'password', $fancy_pattern, false);


$simple_test = new RegExTester();
$simple_test->add_subtest('STATIC', 'URL', $do_absolutely_nothing_pattern);
$simple_test->add_subtest('POST', 'username', $simple_pattern, true, "UNIQUE_SIMPLE");
$simple_test->add_subtest('POST', 'username', $fancy_pattern, false);
$simple_test->add_subtest('POST', 'password', $simple_pattern, true, "UNIQUE_SIMPLE");
$simple_test->add_subtest('POST', 'password', $fancy_pattern, false);

$stay_simple_test = new RegExTester();
$stay_simple_test->add_subtest('POST', 'username', $fancy_pattern, false);
$stay_simple_test->add_subtest('POST', 'password', $fancy_pattern, false);


$fancy_test = new RegExTester();
$fancy_test->add_subtest('STATIC', 'URL', $do_absolutely_nothing_pattern);
$fancy_test->add_subtest('POST', 'username', $fancy_pattern, true, "UNIQUE_FANCY");
$fancy_test->add_subtest('POST', 'password', $fancy_pattern, true, "UNIQUE_FANCY");
$stay_fancy_test = new RegExTester();
// $stay_fancy_test->add_subtest()

$do_absolutely_nothing = new Transition("DO ABSOLUTELY NOTHING", $start_state, $start_state, $do_absolutely_nothing_test);

$fail_again = new Transition("DO NOTHING AGAIN", $failed_login_state, $failed_login_state, $do_nothing_test);
$unfail = new Transition("BACK TO UNFAILED", $failed_login_state, $start_state, $do_absolutely_nothing_test);

$do_nothing = new Transition("DO NOTHING", $start_state, $failed_login_state, $do_nothing_test);



$blind_attack = new Transition("BLIND ATTACK", $start_state, $blind_state, $blind_test );
$simple_attack = new Transition("SIMPLE ATTACK", $start_state, $simple_state, $simple_test);
$fancy_attack = new Transition("FANCY ATTACK", $start_state, $fancy_state, $fancy_test);

$blind_attack2 = new Transition("BLIND ATTACK2", $failed_login_state, $blind_state, $blind_test );
$simple_attack2 = new Transition("SIMPLE ATTACK2", $failed_login_state, $simple_state, $simple_test);
$fancy_attack2 = new Transition("FANCY ATTACK2", $failed_login_state, $fancy_state, $fancy_test);


$stay_blind = new Transition("STAY BLIND", $blind_state, $blind_state, $stay_blind_test );
$blind_to_simple = new Transition("GET SIMPLE", $blind_state, $simple_state, $simple_test);
$blind_to_fancy = new Transition("BLIND TO FANCY", $blind_state, $fancy_state, $fancy_test);

$stay_simple = new Transition("STAY SIMPLE", $simple_state, $simple_state, $stay_simple_test);
$simple_to_fancy = new Transition("SIMPLE TO FANCY", $simple_state, $fancy_state, $fancy_test);

$stay_fancy = new Transition("STAY FANCY", $fancy_state, $fancy_state, $stay_fancy_test);

$test_89 = new Test( array(  $do_absolutely_nothing, 
                                $do_nothing, 
                                $blind_attack, $blind_attack2,
                                $stay_blind, 
                                $simple_attack, $simple_attack2,
                                $stay_simple, 
                                $blind_to_simple,
                                $fancy_attack, $fancy_attack2,
                                $stay_fancy,
                                $blind_to_fancy,
                                $simple_to_fancy, $fail_again, $unfail),
                      $start_state );



?>