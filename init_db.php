<?php
include('Config.class.php');

class ResultError
{
    private $err;
    private $res;

    function __construct($result, $error) {
        $this->res = $result;
        $this->err = $error;
    }
    
    public function Error(){
        return !empty($this->err) ? $this->err : null;
    }

    public function Result(){
        return !empty($this->res) ? $this->res : null;
    }
};

class HandlerModel
{
    protected $db;

    protected function Return($result = null){
        $err = $this->db->error;
        return new ResultError($result, $err);
    }
};

class DatabaseModel extends HandlerModel
{
    
	function __construct($db) {
		$this->db = $db;
	}
    
    public function Drop() {
        $this->db->query('DROP DATABASE IF EXISTS sql_inj_example;');
        return $this->Return('ok');
    }

	public function Create() {
		$this->db->query('CREATE DATABASE IF NOT EXISTS sql_inj_example;');
        return $this->Return('ok');
    }
	
};

class UsersModel extends HandlerModel
{
    
	function __construct($db) {
		$this->db = $db;
	}
    
    public function DropTable() {
        $this->db->query('DROP TABLE IF EXISTS Users;');
        return $this->Return('ok');
    }

	public function CreateTable() {
		$this->db->query('CREATE TABLE IF NOT EXISTS Users(
			id	        INTEGER(10)     NOT NULL    AUTO_INCREMENT,
			login       VARCHAR(50)     NOT NULL,
			password    VARCHAR(50)     NOT NULL,
			PRIMARY KEY (id),
            UNIQUE(login)
        );');
        return $this->Return('ok');
    }
    
    public function Insert($login, $password) {
        $this->db->query("INSERT INTO Users (login, password) VALUES ('$login','$password');");
        return $this->Return($this->db->insert_id);
	}
	
};

class CoinsModel extends HandlerModel
{
    
	function __construct($db) {
		$this->db = $db;
    }
    
    public function DropTable() {
        $this->db->query('DROP TABLE IF EXISTS Coins;');
        return $this->Return('ok');
    }
	 
	public function CreateTable() {
		$this->db->query("CREATE TABLE IF NOT EXISTS Coins(
			id	        INTEGER(10)     NOT NULL    AUTO_INCREMENT,
			user_id     INTEGER(10)     NOT NULL,
			balance     INTEGER(10)     NOT NULL    DEFAULT 0,
			PRIMARY KEY (id)
        );");
        return $this->Return('ok');
    }
    
    public function Insert($userId, $balance) {
        $this->db->query("INSERT INTO Coins (user_id, balance) VALUES ('$userId','$balance');");
        return $this->Return($this->db->insert_id);
	}
	
};

class App extends Config
{
    function __construct() {
        $mysqli = new mysqli(self::HOST, self::USERNAME, self::PASSWORD);
        if ($mysqli->connect_errno) {
            echo "Connect failed: %s\n", $mysqli->connect_error;
            exit();
        }

        // DatabaseModel
        $databaseModel = new DatabaseModel($mysqli);

        echo 'Created database...';
        $databaseModel = $databaseModel->Create();
        if($databaseModel->Error()){
            echo "Error: ".$databaseModel->Error().'<br>';
        } else {
            echo $databaseModel->Result().'<br>';
        }

        $mysqli->close();


        $mysqli = new mysqli(self::HOST, self::USERNAME, self::PASSWORD, self::DB_NAME);
        if ($mysqli->connect_errno) {
            echo "Connect failed: %s\n", $mysqli->connect_error;
            exit();
        }
        
        // UsersModel
        $usersModel = new UsersModel($mysqli);

        echo 'Droped table Users...';
        $dropTable = $usersModel->DropTable();
        if($dropTable->Error()){
            echo "Error: ".$dropTable->Error().'<br>';
        } else {
            echo $dropTable->Result().'<br>';
        }

        echo 'Created table Users...';
        $createTable = $usersModel->CreateTable();
        if($createTable->Error()){
            echo "Error: ".$createTable->Error().'<br>';
        } else {
            echo $createTable->Result().'<br>';
        }

        echo 'Insert user in table Users...';
        $insert = $usersModel->Insert('admin', 'someadminpass');
        if($insert->Error()){
            echo "Error: ".$insert->Error().'<br>';
        } else {
            echo 'ok <br>';
        }

        // CoinsModel
        $coinsModel = new CoinsModel($mysqli);

        echo 'Droped table Coins...';
        $dropTable = $coinsModel->DropTable();
        if($dropTable->Error()){
            echo "Error: ".$dropTable->Error().'<br>';
        } else {
            echo $dropTable->Result().'<br>';
        }

        echo 'Created table Coins...';
        $createTable = $coinsModel->CreateTable();
        if($createTable->Error()){
            echo "Error: ".$createTable->Error().'<br>';
        } else {
            echo $createTable->Result().'<br>';
        }

        echo 'Insert entry in table Coins...';
        $insert = $coinsModel->Insert(1, 250);
        if($insert->Error()){
            echo "Error: ".$insert->Error().'<br>';
        } else {
            echo 'ok <br>';
        }

        $mysqli->close();

    }
};

new App();