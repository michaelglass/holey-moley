<?php
require_once('transition.php');

class Test {
  private $transitions, 
          $state, 
          $suite_id, 
          $transition_to_state,
          $possible_transitions, 
          $start_state;
  public function __construct($transitions, $start_state)
  {
  
    if(is_array($transitions))
      $this->transitions = $transitions;
    else
      throw new Exception("Test needs param transitions to be an array of Transition objects.");
  
    if(is_a($start_state, "State"))
      $this->start_state = $start_state;
    else
      throw new Exception("Test needs param start_state to be an object of type State.");
    
    
    $this->create_transition_hashes();
  }
    
  public function run($db, $suite_id)
  {
    if(is_a($db, 'mysqli') && !$db->connect_error)
      $this->db = $db;
    else
      throw new Exception("Database connection invalid.");  

    $this->suite_id = $suite_id;  
    
    $this->state = $this->get_state($this->start_state);

    //next transition
    $transition = $this->find_next_transition();
    $this->save_transition($transition);
    
    //next state
    $state = $transition->destination();
    
    return $this->state->display();
  }
  
  private function create_transition_hashes()
  {
    $this->transition_to_state = array();
    $this->possible_transitions = array();

    foreach($this->transitions as $transition)
    {
      $name = $transition->name();
      $dest = $transition->destination();
      $source = $transition->source();
      if(isset($this->transition_to_state[$name]))
        throw new Exception("Duplicate transition name, that is invalid.");

      $this->transition_to_state[$name] = $dest;
      
      if( isset($this->possible_transitions[$source->name()]) )
        $this->possible_transitions[$source->name()][] = $transition;
      else
        $this->possible_transitions[$source->name()] = array($transition);
     }
    
  }
  
  private function save_transition($transition)
  {
    $query = "INSERT INTO histories (suite_id, transition_name, final, created_at, updated_at) VALUES($this->suite_id, '".$transition->name()."', ".($transition->destination()->is_final() ? 'TRUE' : 'FALSE').", NOW(), NOW() );";
    if(! $this->db->query($query))
      $error .= "<br/>backend error updating history :(";
    
  }
  
  private function find_next_transition()
  {
    //find transition from state to new state...
    
    foreach($this->possible_transitions[$this->state->name()] as $transition)
      if($transition->can_transition($this->state))
      {
        if(!isset($next_transition))
          $next_transition = $transition;
        else
        {
           throw new Exception("Broken state machine:  More than one possible transitions from " . $state->name() . 'with current params');
           //TODO: DUMP ENV!!
         }  
       }
    
    
    if(!isset($next_transition))
      throw new Exception("Broken state machine, no matching transition from ". $this->state->name());
      
    return $next_transition;
  }
  
  private function get_state($start_state)
  {
    $query = "SELECT transition_name FROM histories WHERE histories.suite_id = '$this->suite_id' ORDER BY created_at desc LIMIT 1;";
    if($result = $this->db->query($query))
    {
      if(mysqli_num_rows($result) == 1 && $row = $result->fetch_row())
        $transition = $row[0];
      else
        $transition = 'NULL';
    }
    else
      throw new Exception("Can't retrieve previous state (probably a database issue)");    
    
    if($transition == 'NULL')
      $state = $start_state;
    else if(isset($this->transition_to_state[$transition]))
      $state = $this->transition_to_state[$transition];
    else
      throw new Exception("Can't find state for transition $transition");
   
    return $state;
  }
}
?>