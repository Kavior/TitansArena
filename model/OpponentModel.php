<?php

require_once('model.php');

class OpponentModel extends Model{
	public function findById($id){
		$opponent = $this->select('arena_opponent', '*', 'opponent_id='.$id);
		if(count($opponent) > 0)
			return $opponent[0];
		return null;
	}
	
	public function findAll(){
		return $this->select('arena_opponent');
	}
}
?>
	