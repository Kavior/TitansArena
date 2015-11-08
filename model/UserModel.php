<?php

require_once('model.php');

class UserModel extends Model{
	public function findById($id){
		$user = $this->select('arena_user', '*', 'user_id = '. $id);
		if($user !== null)
			return $user[0];
	
		return null;
	}
	
	public function findByNickname($nickname){
		$user = $this->select('arena_user', '*', 'user_nickname ="'. $nickname.'"');

		if($user != null)
			return $user[0];
	
		return null;
	}
	//Returns id of the logged user
	public function getLoggedUser(){
		if(isset($_SESSION['user_id'])){
			return $this->findById($_SESSION['user_id']);
		}
		return null;
	}
	//Perform update query on the user
	public function update($column, $value = null, $idUser = null){
		if($idUser == null){
			$idUser = $_SESSION['user_id'];
		}
		
		if(is_array($column)){ //For multiple columns
			foreach($column as $col => $val){
				$this->update($col, $val);
			}
		}else if($value !== null){//Single column
			$query = 'UPDATE arena_user SET ' . $column . ' = "' . $value . '" WHERE user_id=' . $idUser;
			$select = $this->pdo->query($query);
			$select->closeCursor();
		}
	}
	
	/*
	 * Creates new user and returns his id, defaults are set too
	 */
	public function create($nickname, $password, $level = 1, $experience = 0, $totalHP = 100, $strength = 10, $magic = 10, 
		$learningPoints = 0, $skin = 'Dragon Knight.png'){
		$query = 'INSERT INTO arena_user(user_nickname, user_password, user_level, user_experience, user_totalHP, user_strength, user_magic,
			user_learning_points, 	user_skin) 
			VALUES (:nickname, :password, :level, :experience, :totalHP, :strength, :magic, :learningPoints, :skin)';
			
		$prepared = $this->pdo->prepare($query);
		$prepared -> bindValue(':nickname', $nickname, PDO::PARAM_STR);
		$prepared -> bindValue(':password', $password, PDO::PARAM_STR);
		$prepared -> bindValue(':level', $level, PDO::PARAM_STR);
		$prepared -> bindValue(':experience', $experience, PDO::PARAM_STR);
		$prepared -> bindValue(':totalHP', $totalHP, PDO::PARAM_STR);
		$prepared -> bindValue(':strength', $strength, PDO::PARAM_STR);
		$prepared -> bindValue(':magic', $magic, PDO::PARAM_STR);
		$prepared -> bindValue(':learningPoints', $learningPoints, PDO::PARAM_STR);
		$prepared -> bindValue(':skin', $skin, PDO::PARAM_STR);
		
		$prepared->execute();
		
		return $this->pdo->lastInsertId();
	}
	//Get the speed of the user
	public function getSpeed(){
		if(!isset($_SESSION['user_id'])) return 1; //if not logged, return default speed
		
		$userLevel = $this->getLoggedUser()['user_level'];
		$maxSpeed = 8;
		$speed = $userLevel / 20 + 1; //Speed formula
		$speed = $speed <= $maxSpeed ? $speed : $maxSpeed;

		return $speed;
	}

}
?>