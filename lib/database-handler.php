<?php
class Database {
	private $host = DB_HOST;
	private $dbname = DB_NAME;
	private $charset = DB_CHARSET;
	private $user = DB_USER;
	private $password = DB_PASSWORD;
	
	private $dbh;
	private $error;
	private $stmt;
	
	public function __construct() {
		// Set DSN
		$dsn = "mysql:host=".$this->host.";charset=".$this->charset.";dbname=".$this->dbname;
		$options = array (
			PDO::ATTR_PERSISTENT => true,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL
		);

		try {
			$this->dbh = new PDO($dsn, $this->user, $this->password, $options);
		}		
		catch ( PDOException $e ) {
			$this->error = $e->getMessage();
		}
	}
	
	// Prepare statement with query
	public function query($query) {
		$this->stmt = $this->dbh->prepare($query);
	}
	
	// Bind values
	public function bind($param, $value, $type = null) {
		if (is_null($type)) {
			switch (true) {
				case is_int ($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool ($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null ($value):
					$type = PDO::PARAM_NULL;
					break;
				default:
					$type = PDO::PARAM_STR;
			}
		}
		$this->stmt->bindValue($param, $value, $type);
	}
	
	// Execute the prepared statement
	public function execute(){
		return $this->stmt->execute();
	}
	
	// Get result set as array of objects
	public function resultSet() {
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_OBJ);
	}
	
	// Get single record as object
	public function resultRecord() {
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_OBJ);
	}

	public function resultColumn() {
		$this->execute();
		return $this->stmt->fetchColumn();
	}
	
	// Get record row count
	public function rowCount(){
		return $this->stmt->rowCount();
	}
	
	// Returns the last inserted ID
	public function lastInsertId(){
		return $this->dbh->lastInsertId();
	}
}