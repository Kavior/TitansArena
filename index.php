<?php
session_start();
//Include all routes
include ('config/routing.php');

$uri = $_SERVER['REQUEST_URI'];
$uriParts = divideUri($uri);
//Informations needed to choose controller and action
$target = strtolower($uriParts['target']);
$action = strtolower($uriParts['action']);

if($target == ''){
	redirectTo(Router::getUrl('character_view'));
}
//If not logged user wants to sign in or register
if(!isset($_SESSION['user_id']) && $target='user' && ($action == 'login' || $action == 'register')){
	include_once 'controller/userController.php';
	$usersController = new UserController();
	handleFullUrl($usersController, $action);
}else if(isset($_SESSION['user_id'])){ //If user is logged
	switch($target){
		case 'user':
			include_once 'controller/userController.php';
			$usersController = new UserController();
			handleFullUrl($usersController, $action);
			break;
		case 'arena':
			include_once 'controller/arenaController.php';
			$arenaController = new arenaController();
			if($action != ''){
				handleFullUrl($arenaController, $action);
			}else{
				$arenaController->arenaAction();
			}
			break;
			
		case 'character':
			include_once 'controller/characterController.php';
			$characterController = new CharacterController();
			handleFullUrl($characterController, $action);
			break; 
	}
}else{
	$loginUrl = Router::getUrl('user_login'); 
	redirectTo($loginUrl);
}

function redirectTo($url){
	//if(!strpos($url, 'http://'))
		//	$url = 'http://' . $url;
		header('Location:' . $url);
}

//Automatically choose controller and action basing on url
function handleFullUrl($controller, $action){
	$fullActionName = $action . 'Action';
	if(method_exists($controller, $fullActionName))
		$controller->$fullActionName();
}

//Divide the uri into the usable parts
function divideUri($uri){
	$path = parse_url($uri, PHP_URL_PATH);
	$path = str_replace('/Titans%20Arena', '', $path);
	
	if($path[0] == '/') $path = substr($path, 1); //Remove first slash from url, so it can be divided later
	
	$pathParts = explode('/', $path);
	$subActions = array_key_exists(2, $pathParts) ? array_slice($pathParts, 2) : array();
	$target = array_key_exists(0, $pathParts) ? $pathParts[0] : '';
	$action = array_key_exists(1, $pathParts) ? $pathParts[1] : '';
	
	return array(
		'target' => $target,
		'action' => $action,
		'subActions' => $subActions,
		'query' => parse_url($uri, PHP_URL_QUERY)
	);
}

?>