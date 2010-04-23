<?php
class State{
  private $name, $content, $links, $form, $title, $final_flag;
  
  public function __construct($name, $final_flag = false, $content = "", $links = NULL, $form = NULL, $title = NULL)
  {
    $this->final_flag = $final_flag;
    $this->name = $name; 
    $this->content = $content;
    if(is_null($links))
      $this->links = array();
    else if(is_array($links))
      $this->links = $links;
    else
      throw new Exception("Wrong inputs.");
    if(is_null($form))
      $this->form = NULL;
    else if(is_a($form, 'Form') )
      $this->form = $form;
    else
      throw new Exception("Wrong inputs.");
    
    if(is_null($title))
      $this->title = $name;
    else
      $this->title = $title;
  }
  
  public function add_link($link_name, $link_url) { $this->links[] = array($link_name => $link_url); }
    
  public function display($suite_key)
  {
    $output = 
'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
  	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';

    $output .= 
" <title>$this->title</title>
</head>
  <body>";
  $output .= "<p>$this->content</p>";
  $links_output = '';
  if(count($this->links) > 0 )
  {
    foreach($this->links as $link_name => $link_url)
      $links_output .= "<a href=\"$link_url\">$link_name</a><br/>";
    $output .= "<p>$links_output</p>";
  }
  if(!is_null($this->form))
    $output .= $this->form->display($suite_key);
  $output .= "  </body>
</html>";
    return $output;
  }

  public function is_equal_to($other)
  {
    return (is_a($other, "State") && $this->name == $other->name && $this->title == $other->title && $this->content == $other->content); //links and form??
  }
  
  public function name() {return $this->name; }
  public function is_final() { return $this->final_flag; }
}

class Form{
  private $action, $is_POST, $inputs;
  
  public function __construct($action, $inputs = NULL, $is_POST = true)
  {
    $this->action = $action;
    if(is_array($inputs))
      $this->inputs = $inputs;
    else
      $this->inputs = array();
    
    $this->is_POST = $is_POST;
  }
  
  public function add_input($input_name) { $this->inputs[] = $input_name; }
  
  public function display($suite_key)
  {
    $output = "\n\n<p><form method='".($this->is_POST? 'POST' : 'GET')."' action='/$suite_key/$this->action'>\n";
    foreach($this->inputs as $input_name)
      $output .= "<input type='text' name='$input_name'/><br/>\n";
    $output .= "<input type=\"submit\"/></form></p>\n";
    return $output;
  }
}

?>