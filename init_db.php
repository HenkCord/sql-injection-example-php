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
        return new ResultError($result, $this->db->error);
    }
};

class UsersModel extends HandlerModel
{
    
	function __construct($db) {
		$this->db = $db;
	}
	 
    public function DropTable() {
        $this->db->query('DROP TABLE Users');
        return $this->Return('ok');
    }
    
	public function CreateTable() {
		$this->db->query("CREATE TABLE Users(
			id	        INTEGER(10)     NOT NULL    AUTO_INCREMENT,
			login       VARCHAR(50)     NOT NULL,
			password    VARCHAR(50)     NOT NULL,
			PRIMARY KEY (id),
            UNIQUE(login)
        )");
        return $this->Return('ok');
	}
	
	public function Find($login, $password) {
		$this->db->query("SELECT id FROM Users LIMIT 0,1");
	}
	
};


class App extends Config
{
    function __construct() {
        $mysqli = new mysqli(self::HOST, self::USERNAME, self::PASSWORD, self::DB_NAME);
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }

        $usersModel = new UsersModel($mysqli);
        $createTable = $usersModel->CreateTable();
        if($createTable->Error()){
            print("Error: ".$createTable->Error());
            exit();
        }
        printf($createTable->Result());
    }
};

new App();