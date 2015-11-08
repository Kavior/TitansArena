<?php

include 'controller/controller.php';
class UserController extends Controller{
	
	public function registerAction(){
		if(!$this->isUserLogged()){
			
			$nickname = isset($_POST['nickname']) && !empty($_POST['nickname']) ? $_POST['nickname'] : null;
			$passwordFirst = isset($_POST['pass_first']) && !empty($_POST['pass_first']) ? $_POST['pass_first'] : null;
			$passwordSecond = isset($_POST['pass_second']) && !empty($_POST['pass_second']) ? $_POST['pass_second'] : null;
			
			$formErrors = array();	
				
			if($nickname !== null && $passwordFirst !== null && $passwordSecond !== null){			
				if($passwordFirst === $passwordSecond){
					$userModel = $this->loadModel('User');
					
					if($userModel->findByNickname($nickname) === null){ //Check if user with that name already exists
						$userPassword = md5($passwordFirst);
						$userId = $userModel->create($_POST['nickname'], $userPassword);
						$_SESSION['user_id'] = $userId;
						$this->redirectToRoute('character_view');
					}else{
						$formErrors[] = 'User with that nickname already exists';
					}
				}else{
					$formErrors[] = 'Entered passwords don\'t match';
				}
			}else if(!empty($_POST)){
				if($nickname == null){
					$formErrors[] = 'Enter the nickname';
				}
				
				if($passwordFirst == null || $passwordSecond == null){
					$formErrors[] = 'Enter both passwords';
				}
			}
			
			$loginUrl = $this->generateUrl('user_login');
			require_once('view/register.php');
		}else{
			$this->redirectToRoute('character_view');
		}
	}
	
	public function loginAction(){
		if(!$this->isUserLogged()){
			$username = isset($_POST['nickname']) ? $_POST['nickname'] : null;
			$password = isset($_POST['password']) ? $_POST['password'] : null;
			
			$formErrors = array();	
			$wrongData = false;
			
			if($username != null && $password != null){
				$userModel = $this->loadModel('User');
				$user = $userModel->findByNickname($username);
				if($user !== null){
					$passEncrypted = md5($password);
					
					if($user['user_password'] === $passEncrypted){
						$_SESSION['user_id'] = $user['user_id'];
						$this->redirectToRoute('character_view');
					}else{
						$wrongData = true;
					}
				}else{
					$wrongData = true;
				}
			}else if(!empty($_POST)){
				if($username == null){
					$formErrors[] = 'Enter the nickname';
				}
				
				if($password == null){
					$formErrors[] = 'Enter the password';
				}
			}
			
			if($wrongData)
				$formErrors[] = 'Invalid username or password';
			
			$registerUrl = $this->generateUrl('user_register');
			require_once('view/login.php');
		}else{
			$this->redirectToRoute('character_view');
		}
	}
	
	public function logoutAction(){
		setcookie(session_name(), '', 100);
		session_unset();
		session_destroy();
		$_SESSION = array();
		$this->redirectToRoute('user_login');
	}
}
?>