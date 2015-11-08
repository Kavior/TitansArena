<?php
include_once 'controller/controller.php';

class arenaController extends Controller{
	//Main arena
	public function arenaAction(){
			//Send opponent data to the view
			if(isset($_POST['opponentInfoId'])){
				echo $this->getOpponentJSON($_POST['opponentInfoId']);
				return 1; //true
			}
			
			//There must me set opponent and difficulty in order to start fight
			if(isset($_GET['opponent']) && isset($_GET['diff'])){
				$difficulty = $_GET['diff'];
				$opponentId = $_GET['opponent'];
				
				$opponentModel = $this->loadModel('Opponent');
				$chosenOpponent = $opponentModel->findById($opponentId);
				
				$opponentJSON = $this->getOpponentJSON($opponentId);
				if($chosenOpponent == null || $difficulty < 0 || $difficulty > 2){//Wrong data
					$this->redirectToRoute('arena_choice');
				}
				
				$userModel = $this->loadModel('User');
				$user = $userModel->findById($_SESSION['user_id']);
	
				$characterUrl = $this->generateUrl('character_view');
				$characterSpeed = $userModel->getSpeed();

			}else{ //If not chosen, return to opponent choice
				$this->redirectToRoute('arena_choice');
			}
		
		require_once('view/arena.php');
	}
	
	//Choice of opponent and difficulty
	public function choiceAction(){
		$opponentModel = $this->loadModel('Opponent');

		$order = 'opponent_name';
		$opponents = $opponentModel->select('arena_opponent', '*', null, $order); //Order by name
		$arenaUrl = $this->generateUrl('arena');
		
		require_once('view/arena_choice.php');
	}
	
	//Info about the game
	public function infoAction(){
		require_once('view/arena_info.php');
	}
	
	//Returns information about opponent in JSON format
	public function getOpponentJSON($opponentId){
		$opponentModel = $this->loadModel('Opponent');
		$chosenOpponent = $opponentModel->findById($opponentId);
		
		return json_encode($chosenOpponent);
	}
}
?>
	