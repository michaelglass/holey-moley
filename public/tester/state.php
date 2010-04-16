<?php
class State{
  private $name, $content, $links, $form, $title;
  
  public function __construct($name, $content, $links, $form, $title = NULL)
  {
    $this->$name = $name; 
    $this->$content = $content;
    if(is_array($links))
      $this->$links = $links;
    else
      throw new Exception("Wrong inputs.");
    if(is_a($form, 'Form') )
      $this->$form = $form;
    else
      throw new Exception("Wrong inputs.");
    
    if(is_null($title))
      $this->$title = $name;
    else
      $this->$title = $title;
  }
  
  public function add_link($link_name, $link_url) { $links[] = array($link_name => $link_url); }
    
  public function display()
  {
    $output = 
'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
  	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';

    $output .= 
" <title>$title</title>
</head>
  <body>";
  $output .= "<p>$content</p>";
  $links_output = '';
  foreach($links as $link_name => $link_url)
    $links_output .= "<a href=\"$link_url\">$link_name</a><br/>";
  $output .= "<p>$links_output</p>";
  $output .= $form->display();
  $output .= "  </body>
</html>";
    return $output;
  }
  
}

class Form{
  private $action, $is_POST, $inputs;
  
  public function __construct($action, $inputs = NULL, $is_POST = true)
  {
    $this->$action = $action;
    if(is_array($inputs))
      $this->$inputs = $inputs;
    else
      throw new Exception("Wrong inputs.");
    
    $this->$is_POST = $is_POST;
  }
  
  public function add_input($input_name) { $inputs[] = $input_name; }
  
  public function display()
  {
    $output = "\n\n<p><form method='".($is_POST? 'POST' : 'GET')."' action='$action'>\n";
    foreach($inputs as $input_name)
      $output .= "<input type='text' name='$input_name'/><br/>\n"  ;
    $output .= "</form></p>\n";
    return $output;
  }
}

?>