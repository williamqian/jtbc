<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace jtbc {
  class pagi
  {
    protected $db;
    public $rslimit = 0;
    public $pagesize = 0;
    public $sqlstr = '';
    public $rscount = 0;
    public $pagenum = 0;
    public $pagetotal = 0;

    protected function getRsCount()
    {
      $rscount = 0;
      $sqlstr = "select count(*) from " . base::getLRStr(base::getLRStr($this -> sqlstr, 'from', 'rightr'), 'order by', 'leftr');
      $rq = $this -> db -> query($sqlstr);
      $rs = $rq -> fetch();
      if (is_array($rs)) $rscount = base::getNum($rs[0], 0);
      return $rscount;
    }

    public function getDataAry($argSqlstr, $argPageNum, $argPageSize, $argRsLimit = 0)
    {
      $dataAry = array();
      $this -> sqlstr = $argSqlstr;
      $this -> pagenum = base::getNum($argPageNum, 0);
      $this -> pagesize = base::getNum($argPageSize, 0);
      $this -> rslimit = base::getNum($argRsLimit, 0);
      $this -> rscount = $this -> getRsCount();
      if ($this -> pagesize == 0) $this -> pagesize = 20;
      if ($this -> rslimit == 0) $this -> rslimit = $this -> rscount;
      else
      {
        if ($this -> rscount < $this -> rslimit) $this -> rslimit = $this -> rscount;
      }
      if ($this -> rslimit % $this -> pagesize == 0) $this -> pagetotal = floor($this -> rslimit / $this -> pagesize);
      else $this -> pagetotal = floor($this -> rslimit / $this -> pagesize) + 1;
      if ($this -> pagenum == 0) $this -> pagenum = 1;
      $pagesize = $this -> pagesize;
      $rslimit = $this -> pagesize * $this -> pagenum;
      if ($rslimit > $this -> rslimit)
      {
        $rslimit = $this -> rslimit;
        $pagesize = $rslimit - ($this -> pagesize * ($this -> pagenum - 1));
      }
      if ($rslimit > 0 && $pagesize > 0)
      {
        $index = 0;
        $sqlstr = $this -> sqlstr . ' limit ' . ($rslimit - $pagesize) . ',' . $pagesize;
        $rq = $this -> db -> query($sqlstr);
        while($rs = $rq -> fetch())
        {
          $dataAry[$index] = $rs;
          $index += 1;
        }
      }
      return $dataAry;
    }

    function __construct($argDb)
    {
      $this -> db = $argDb;
    }
  }
}
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
?>