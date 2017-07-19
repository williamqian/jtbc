<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace jtbc {
  use PDO;
  use PDOException;
  class db
  {
    public $conn;
    public $dbHost;
    public $dbUsername;
    public $dbPassword;
    public $dbDatabase;
    public $errStatus = 0;
    public $errMessage;
    public $lastInsertId;

    public function init()
    {
      try {
        $this -> conn = @new PDO("mysql:host=" . $this -> dbHost . ";dbname=" . $this -> dbDatabase, $this -> dbUsername, $this -> dbPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'', PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION));
      }
      catch (PDOException $e) {
        $this -> errStatus = 1;
        $this -> errMessage = $e -> getMessage();
      }
    }

    public function query($argSQLString)
    {
      $sqlString = $argSQLString;
      $query = $this -> conn -> query($sqlString);
      return $query;
    }

    public function exec($argSQLString)
    {
      $sqlString = $argSQLString;
      $exec = $this -> conn -> exec($sqlString);
      if (substr($sqlString, 0, 6) == 'insert') $this -> lastInsertId = $this -> conn -> lastInsertId();
      return $exec;
    }

    public function desc($argTable)
    {
      $table = $argTable;
      $query = $this -> query('desc ' . $table);
      $desc = $query -> fetchAll(PDO::FETCH_ASSOC);
      return $desc;
    }
  }
}
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
?>