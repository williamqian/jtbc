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
        $dsn = 'mysql:host=' . $this -> dbHost;
        if (!empty($this -> dbDatabase)) $dsn .= ';dbname=' . $this -> dbDatabase;
        $this -> conn = @new PDO($dsn, $this -> dbUsername, $this -> dbPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'', PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION));
      }
      catch (PDOException $e) {
        $this -> errStatus = 1;
        $this -> errMessage = $e -> getMessage();
      }
    }

    public function fetch($argSQLString, $argMode = 0)
    {
      $rs = null;
      $sqlString = $argSQLString;
      $mode = $argMode;
      $rq = $this -> conn -> query($sqlString);
      if ($mode == 1) $rs = $rq -> fetch(PDO::FETCH_ASSOC);
      else $rs = $rq -> fetch();
      return $rs;
    }

    public function fetchAll($argSQLString, $argMode = 0)
    {
      $rs = null;
      $sqlString = $argSQLString;
      $mode = $argMode;
      $rq = $this -> conn -> query($sqlString);
      if ($mode == 1) $rs = $rq -> fetchAll(PDO::FETCH_ASSOC);
      else $rs = $rq -> fetchAll();
      return $rs;
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

    public function showFullColumns($argTable)
    {
      $table = $argTable;
      $query = $this -> query('show full columns from ' . $table);
      $desc = $query -> fetchAll(PDO::FETCH_ASSOC);
      return $desc;
    }
  }
}
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
?>