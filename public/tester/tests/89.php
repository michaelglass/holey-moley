<?php
$form = new Form("login.php");
$form->add_input('username');
$form->add_input('password');

$start_state = new State("start", false, "Login to FakeCo!<br/>", NULL, $form, "login")
$failed_login = new State("failed", false, "Login Failed, please try again<br/>Login to FakeCo!<br/>", NULL, $form, "login");

$blind_attack = new State("blind", true, "SYNTAX ERROR.  HERE IS ALL OF OUR IMPORTANT INFORMATION:<br/>FAVORITE COLOR: RED<br/>", NULL, $form, "blind attack succeeded"  );
$simple_attack = new State("simple", true, "you have logged in as bob<br/>", NULL, $form, "simple attack successful");
$advanced_attack = new State("advanced", true, "you have logged in as SUPERBOB (you are 1337)<br/>", NULL, $form, "advanced attack successful");

$blind_pattern = "/\'|\"|\)/";
$simple_pattern = "/\w*'\s*(--|#|or)/i";
$advanced_pattern = "/(\/\*.*(\/\*)?)|(;|union)|(0x|char)/i";

$do_absolutely_nothing_pattern = "/login.php/i"

$do_absolutely_nothing_test = new RegExTester();
$do_absolutely_nothing_test->add_subtest('STATIC', 'URL', $do_absolutely_nothing_pattern);
$do_absolutely_nothing_test->add_subtest('POST', 'username', $blind_pattern, false);
$do_absolutely_nothing_test->add_subtest('POST', 'username', $simple_pattern, false);
$do_absolutely_nothing_test->add_subtest('POST', 'username', $advanced_pattern, false);
$do_absolutely_nothing_test->add_subtest('POST', 'password', $blind_pattern, false); #perhaps we want to allow for [] of fields to check over...
$do_absolutely_nothing_test->add_subtest('POST', 'password', $simple_pattern, false);
$do_absolutely_nothing_test->add_subtest('POST', 'password', $advanced_pattern, false);

$do_nothing_test = new RegExTester();
$do_nothing_test->add_subtest('POST', 'username', $blind_pattern, false);
$do_nothing_test->add_subtest('POST', 'username', $simple_pattern, false);
$do_nothing_test->add_subtest('POST', 'username', $advanced_pattern, false);
$do_nothing_test->add_subtest('POST', 'password', $blind_pattern, false); #perhaps we want to allow for [] of fields to check over...
$do_nothing_test->add_subtest('POST', 'password', $simple_pattern, false);
$do_nothing_test->add_subtest('POST', 'password', $advanced_pattern, false);

$blind_test = new RegExTester();
$blind_test->add_subtest('POST', 'username', $blind_pattern);
$blind_test->add_subtest('POST', 'username', $simple_pattern, false);
$blind_test->add_subtest('POST', 'username', $advanced_pattern, false);
$blind_test->add_subtest('POST', 'password', $blind_pattern);
$blind_test->add_subtest('POST', 'password', $simple_pattern, false);
$blind_test->add_subtest('POST', 'password', $advanced_pattern, false);

$simple_test = new RegExTester();
$blind_test->add_subtest('POST', 'username', $simple_pattern);
$blind_test->add_subtest('POST', 'username', $advanced_pattern, false);
$blind_test->add_subtest('POST', 'password', $simple_pattern);
$blind_test->add_subtest('POST', 'password', $advanced_pattern, false);


?>