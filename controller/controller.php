<?php
require_once('config/routing.php');

abstract class Controller{

   public function redirectToRoute($route){
   		$this->redirect( $this->generateUrl($route) );
   }
   
   //Generate url from route
    public function generateUrl($route, $parameters = null){
    	return Router::getUrl($route, $parameters);
    }
	
    //Redirect to URL
    public function redirect($url) {
    	//if(!strpos($url, 'http://'))
		//	$url = 'http://' . $url;
 		header("location: ".$url);
    }
	
	public function getLoggedUser(){
		$userModel = $this->loadModel('User');
		return $userModel->getLoggedUser();
	}

    public function loadModel($name, $path='model/') {
        $model = $path . $name . 'Model.php';
        $modelName = $name . 'Model';

        try {
            if(is_file($model)) {
                require_once $model;
				$getModel = new $modelName();
            } else {
                throw new Exception('Unable to find a model with name "' . $modelName . '".');
            }
        }catch(Exception $e) {
           echo  $e->getMessage() . ' in' . $e->getFile() . ' in line ' . $e->getLine();
        }
		return $getModel;
    }
	
	public function isUserLogged(){
		return isset($_SESSION['user_id']);
	}
}
?>