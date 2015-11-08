<?php
include 'controller/controller.php';

class CharacterController extends Controller{
	//Preview of the character stats
	public function indexAction(){
		$skinsPath = 'resources/images/skins';
		$skinsDir = scandir($skinsPath);
		$allSkinsImages = array_diff($skinsDir, array('.', '..')); //Get rid of directories signs
		$allSkinsImages = array_values($allSkinsImages); //Reset array values
		
		$skinsJSON = json_encode($allSkinsImages);
		
		$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

		if($userId !== null){
			$userModel = $this->loadModel('User');
			$user = $userModel->findById($userId);
			
			//Add experience after won fight
			if(isset($_POST['addExperience'])){
				$addedExp = $_POST['addExperience'];
				$this->addExperience($addedExp);
				$userExp = $user['user_experience'];

				if($userExp + $addedExp  > $this->getNextLevelExperience($user['user_level'])){
					$this->advanceToProperLevel();
				}
				return 1;
			}
			
			//If there is healthPoints posted, that means that user abilities were requested to save
			if(isset($_POST['healthPoints'])){
				$newHp = $_POST['healthPoints'];
				$newLearningPoints = $_POST['learningPoints'];
				$newMagic = $_POST['magic'];
				$newStrength = $_POST['strength'];
				
				$updateData = array(
					'user_totalHP' => $newHp,
					'user_strength' => $newStrength,
					'user_magic' => $newMagic,
					'user_learning_points' => $newLearningPoints,
				);
				
				$userModel->update($updateData);
			}
			
			//Player was killed
			if(isset($_POST['playerKilled'])){
				$this->killPlayer();
			} 
				
			//Skin was changed	
			if(isset($_POST['newSkin'])){
				$userModel->update('user_skin', $_POST['newSkin']);
			}
					
			$anyLpAvailable = (int) ($user['user_learning_points'] > 0);
			$userLearningPoints = (int) $user['user_learning_points'];
			$characterUrl = $this->generateUrl('character_view');
			$nextLevelXp = $this->getNextLevelExperience($user['user_level']);
		}
		
		$arenaChoiceUrl = $this->generateUrl('arena_choice');
		$userSkinFile = $user['user_skin'];
		$skinName = preg_replace('/\.[a-zA-Z]{2,3}$/', '', $userSkinFile);

		require_once('view/character.php');

	}

	//Decrease player abilities because of death
	public function killPlayer(){
		if($user = $this->getLoggedUser()){
			$userExperience = $user['user_experience'];
			$userLevel = $user['user_level'];
			$userHp = $user['user_totalHP'];
			$userMagic = $user['user_magic'];
			$userStrength = $user['user_strength'];
			
			$oneLevelExperience = $userExperience - $this->getMinLevelExperience($userLevel - 1);

			$xpAfterDeath = $userExperience * 0.9 - $oneLevelExperience - 80;
			if($xpAfterDeath < 0) $xpAfterDeath = 0;
			
			$hpAfterDeath = floor($userHp * 0.95) - 10;
			if($hpAfterDeath < 100) $hpAfterDeath = 100;
			
			$magicAfterDeath = floor($userMagic*0.95) - 4;
			if($magicAfterDeath < 10) $magicAfterDeath = 10;
			
			$strengthAfterDeath = floor($userStrength*0.95) - 4;
			if($strengthAfterDeath < 10) $strengthAfterDeath = 10;
			
			
			$levelAfterDeath = $this->countLevelByExperience($xpAfterDeath) > 0 ? $this->countLevelByExperience($xpAfterDeath) : 1;
			
			$userModel = $this->loadModel('User');
			$newAbilities = array(
				'user_level' => $levelAfterDeath,
				'user_experience' => $xpAfterDeath,
				'user_totalHP' => $hpAfterDeath,
				'user_magic' => $magicAfterDeath,
				'user_strength' => $strengthAfterDeath
			);
			$userModel->update($newAbilities);
		}
	}
	
	public function advanceToProperLevel($userId = null){
		if($userId == null){
			$userId = $_SESSION['user_id'];
		}
		
		$userModel = $this->loadModel('User');
		$user = $userModel->findById($userId);
		
		$userLevel = $user['user_level'];
		$userExperience = $user['user_experience'];
		$userLearningPoints = $user['user_learning_points'];
		
		$levelByExp = $this->countLevelByExperience($userExperience);
		$levelDifference = $levelByExp - $userLevel;
			
		if($levelDifference > 0){ //Advance
			$lpAfterAdvance = $userLearningPoints + $levelDifference * 10; //10 LP per level
			$levelAfterAdvance = $this->countLevelByExperience($userExperience); 
			$userModel->update(array(
				'user_level' => $levelAfterAdvance,
				'user_learning_points' => $lpAfterAdvance
			));
		}		
	}
	
	public function advanceToNextLevel($userId = null){
		if($userId == null){
			$userId = $_SESSION['user_id'];
		}
		
		$userModel = $this->loadModel('User');
		$user = $userModel->findById($userId);
		
		$userLevel = $user['user_level'];
		$userModel->update('user_level', $userLevel + 1, $userId);
	}
	
	public function addExperience($exp, $userId = null){
		if($userId == null){
			$userId = $_SESSION['user_id'];
		}
		
		$userModel = $this->loadModel('User');
		$userExp = $userModel->findById($userId)['user_experience'];
		$newExp = $userExp + $exp;
		$userModel->update('user_experience', $newExp, $userId);
	}
	
	public function getNextLevelExperience($currentLevel){
		return $this->getMinLevelExperience($currentLevel + 1);
	}
	
	public function getMinLevelExperience($level){
		return ceil( $level* 0.8 * pow($level, 3) );
	}
	
	public function countLevelByExperience($experience){
		$level = floor( pow(((10/8) * $experience), (1/4)) );
		return $level;
	}
}
?>