<?php
  $subject = "\'union, password=>, )'";
  $pattern = "/(\/\*.*(\/\*)?)|(;|union)|(0x|char)/i";
  $passed = preg_match($pattern, $subject);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>figuring out php...</title>
  </head>
  <body>
    <?= $passed ? 'passed' : 'failed' ?>
  </body>
</html>"


