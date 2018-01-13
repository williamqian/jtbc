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
    public $dbStructureCache = false;
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
      $desc = null;
      $cacheName = 'db_structure_desc_' . $table;
      if ($this -> dbStructureCache == true) $desc = cache::get($cacheName);
      if (empty($desc))
      {
        $query = $this -> query('desc ' . $table);
        $desc = $query -> fetchAll(PDO::FETCH_ASSOC);
        if ($this -> dbStructureCache == true) @cache::put($cacheName, $desc);
      }
      return $desc;
    }

    public function showFullColumns($argTable)
    {
      $table = $argTable;
      $fullColumns = null;
      $cacheName = 'db_structure_fullcolumns_' . $table;
      if ($this -> dbStructureCache == true) $fullColumns = cache::get($cacheName);
      if (empty($fullColumns))
      {
        $query = $this -> query('show full columns from ' . $table);
        $fullColumns = $query -> fetchAll(PDO::FETCH_ASSOC);
        if ($this -> dbStructureCache == true) @cache::put($cacheName, $fullColumns);
      }
      return $fullColumns;
    }
  }
}
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
?>