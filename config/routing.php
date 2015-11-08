<?php
class Router{
	public static $routes = array(
		'character_view' => '/character/index',
		'user_login' => '/user/login',
		'user_logout' => '/user/logout',
		'user_register' => '/user/register',
		'arena' => '/arena',
		'arena_choice' => '/arena/choice',
		'arena_info' => '/arena/info'
		
	);
	
	/*
	 * Create url from route
	 */
	public static function getUrl($route, $parameters = null){
		$routes = self::$routes;
		
		if(array_key_exists($route, $routes)){
			$host = $_SERVER['HTTP_HOST'];
			$uri = $_SERVER['REQUEST_URI'];
			
			if(strpos($uri, 'Titans%20Arena'))
				$newUri = '/Titans%20Arena';
			else
				$newUri = '';

			$parametrs = $routes[$route];
			$newUri .= $parametrs;
			
			$url = 'http://' . $host . $newUri;
			if($parameters != null)
				$url .= $parameters;

			return $url;
		}else{
			return null;
		}
	}
}
?>
