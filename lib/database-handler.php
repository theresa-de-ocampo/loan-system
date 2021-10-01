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
	private $logFile = "../src/log.txt";
	
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
		catch (PDOException $e) {
			$this->error = $e->getMessage()." in ".$e->getFile()." on line ".$e->getLine();
			$this->terminate();
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

	public function bindAndExecute($data) {
		return $this->execute($data);
	}

	public function startTransaction() {
		$this->dbh->beginTransaction();
	}

	public function commit() {
		$this->dbh->commit();
	}

	public function rollBack() {
		$this->dbh->rollBack();
	}

	public function executeWithoutCatch() {
		return $this->stmt->execute();
	}
	
	// Execute the prepared statement
	public function execute($message = "", $redirect = "") {
		try {
			return $this->stmt->execute();
		}
		catch (PDOException $e) {
			$this->logFile = "../src/log.txt";
			$this->error = $e->getMessage()." in ".$e->getFile()." on line ".$e->getLine();
			$this->logError();
		}
		finally {
			if ($redirect !== "") {
				$this->confirmQuery($message, $redirect);
			}
		}
	}
	
	// Get result set as array of objects
	public function resultSet() {
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_OBJ);
	}

	// Get result set as a one-dimensional array columns (1 column per record)
	public function resultSetOneColumn() {
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_COLUMN);
	}
	
	// Get single record as object
	public function resultRecord() {
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_OBJ);
	}

	// Get single column from the next row of a result set or false if there are no more rows.
	public function resultColumn() {
		$this->execute();
		return $this->stmt->fetchColumn();
	}
	
	// Get record row count
	public function rowCount() {
		return $this->stmt->rowCount();
	}
	
	// Returns the last inserted ID
	public function lastInsertId() {
		return $this->dbh->lastInsertId();
	}

	public function __destruct() {
		$this->dbh = null;
	}

	private function terminate() {
		$this->logFile = "src/log.txt";
		$this->logError();
		echo "<script>window.location.href='error-505.php'</script>";
		die();
	}

	public function logError($error = "") {
		$time = date("Y-m-d H:i", time());
		if ($error !== "")
			$contents = "$time\t$error\r";
		else
			$contents = "$time\t$this->error\r";
		file_put_contents($this->logFile, $contents, FILE_APPEND);
	}

	public function confirmQuery($message, $redirect) {
		if ($this->stmt->rowCount() > 0) {
			if ($message !== "") {
				echo "<script>alert('$message');</script>";
			}
		}
		else {
			echo "<script>alert('An unexpected error occurred. Please try again later.');</script>";
		}
		echo "<script>window.location.replace('$redirect');</script>";
	}

	public function confirmQueryWithReceipt($redirect) {
		if ($this->stmt->rowCount() > 0) {
			echo "<script>window.location.replace('$redirect');</script>";
		}
		else {
			echo "<script>alert('An unexpected error occurred. Please try again later.');</script>";
		}
	}
}