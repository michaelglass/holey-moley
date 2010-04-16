<?php
class Test {
  private $transitions, 
          $db, 
          $state, 
          $suite_id, 
          $transition_to_state,
          $possible_transitions;
  public function __construct($db, $suite_id, $transitions)
  {
    if(is_a($db, 'mysqli') && !$db->connect_error)
      $this->db = $db;
    else
      throw new Exception("Database connection invalid.");  
  
    if(is_array($transitions))
      $this->transitions = $transitions;
    else
      throw new Exception("Test needs param transitions to be an array of Transition objects.");
  
    create_transition_hashes();
            
    $this->state = get_state();

    //next transition
    $transition = find_next_transition();
    save_transition($transition);
    
    //next state
    $state = $transition->destination();   
  }
  
  public function display()
  {
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

      if( isset($this->possible_transitions[$source]) )
        $this->possible_transitions[$source][] = $transition;
      else
        $this->possible_transitions[$source] = array($transition);
     }
    
  }
  
  private function save_transition($transition)
  {
    $query = "INSERT INTO histories (suite_id, transition_name, final, created_at, updated_at) VALUES($this->suite_id, '$transition->name()', ".($finals[$transition] ? 'TRUE' : 'FALSE').", NOW(), NOW() );";
    if(! $db->query($query))
      $error .= "<br/>backend error updating history :(";
    
  }
  
  private function find_next_state()
  {
    //find transition from state to new state...
    foreach($possible_transitions[$state] as $transition)
      if($transition->can_transition($state))
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
      throw new Exception("Broken state machine, no matching transition from $state");
      
    return $next_transition;
  }
  
  private function get_state()
  {
    $query = "SELECT transition_name FROM histories WHERE histories.suite_id = '$suite_id' ORDER BY created_at desc LIMIT 1;";
    if($result = $db->query($query)) {
      if(mysqli_num_rows($result) == 1 && $row = $result->fetch_row())
        $transition = $row[0];
      else
        $transition = 'NULL';
    }
    else
      throw new Exception("Can't retrieve previous state (probably a database issue)");    
    
    if($transition == 'NULL')
      $state = "START";
    else if(isset($transition_to_state[$state]))
      $state = $transition_to_state[$state];
    else
      throw new Exception("Can't find state for transition $transition");
    
    return $state;
  }
}
?>