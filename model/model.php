<?php

abstract class Model{
 
    public function  __construct() {
 		try {
            require 'config/db.php';
            $this->pdo = new \PDO('mysql:host='.$host.';dbname='.$db, $user, $password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }catch(DBException $e) {
            echo 'Cannot connect to the database';
        }
    }

    public function loadModel($name, $path='') {
 		$model = $path . $name . '.php';
        $modelName = $name . 'Model';
        
        try {
            if(is_file($model)) {
                require $model;
            } else {
                throw new \Exception('Unable to load a model');
            }
        }catch(\Exception $e) {
           echo  $e->getMessage() . ' in' . $e->getFile() . ' in line ' . $e->getLine();
        }

    }
	//Perform select action on model
    public function select($from, $what = '*', $where = null, $order = null, $limit = null) {
 		$query = 'SELECT ' . $what . ' FROM ' . $from;
		$result = array();
        if($where != null)
            $query .= ' WHERE ' . $where;
        if($order != null)
            $query .= ' ORDER BY ' . $order;
        if($limit != null)
            $query .= ' LIMIT ' . $limit;
 
        $select = $this->pdo->query($query);
        foreach ($select as $row) {
            $result[] = $row;
        }
        $select->closeCursor();
 
        return $result;
    }
}
?>