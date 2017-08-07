<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace jtbc\universal {
  use jtbc\base;
  use jtbc\cache;
  use jtbc\page;
  use jtbc\smart;
  use jtbc\tpl;
  class category
  {
    public static function getAllGenre()
    {
      $allGenre = array();
      $base = smart::getActualRoute('./');
      $folder = smart::getFolderByGuide('category');
      $folderAry = explode('|+|', $folder);
      foreach($folderAry as $key => $val)
      {
        if (!base::isEmpty($val))
        {
          $val = base::getLRStr($val, $base, 'rightr');
          array_push($allGenre, $val);
        }
      }
      return $allGenre;
    }

    public static function getAllGenreSelect($argAllGenre = null, $argGenre = '')
    {
      $tmpstr = '';
      $allGenre = $argAllGenre;
      $genre = $argGenre;
      if (!is_array($allGenre)) $allGenre = self::getAllGenre();
      $optionUnselected = tpl::take('global.config.xmlselect_unselect', 'tpl');
      $optionselected = tpl::take('global.config.xmlselect_select', 'tpl');
      foreach ($allGenre as $key => $val)
      {
        if ($val == $genre) $tmpstr .= $optionselected;
        else $tmpstr .= $optionUnselected;
        $tmpstr = str_replace('{$explain}', base::htmlEncode(tpl::take('global.' . $val . ':category.title', 'cfg') . ' [' . $val . ']'), $tmpstr);
        $tmpstr = str_replace('{$value}', base::htmlEncode($val), $tmpstr);
      }
      return $tmpstr;
    }

    public static function getCategoryAryByGenre($argGenre, $argLang)
    {
      $genre = $argGenre;
      $lang = base::getNum($argLang, 0);
      if (!base::isEmpty($genre))
      {
        $cacheName = 'universal-category-array-' . $genre . '-' . $lang;
        $categoryAry = cache::get($cacheName);
        if (empty($categoryAry))
        {
          $categoryAry = self::getDBCategoryAryByGenre($genre, $lang);
          cache::put($cacheName, $categoryAry);
        }
      }
      return $categoryAry;
    }

    public static function getCategoryChildID($argGenre, $argLang, $argFID)
    {
      $tmpstr = '';
      $genre = $argGenre;
      $lang = base::getNum($argLang, 0);
      $fid = base::getNum($argFID, 0);
      if (!base::isEmpty($genre))
      {
        $categoryAry = self::getCategoryAryByGenre($genre, $lang);
        if (is_array($categoryAry))
        {
          $prefix = self::getPrefix();
          foreach ($categoryAry as $key => $val)
          {
            if (is_array($val))
            {
              $rsid = base::getNum($val[$prefix . 'id'], 0);
              $rsfid = base::getNum($val[$prefix . 'fid'], -1);
              if ($rsfid == $fid)
              {
                $tmpstr .= $rsid . ',';
                $tmpstr .= self::getCategoryChildID($genre, $lang, $rsid);
              }
            }
          }
        }
      }
      if (!base::isEmpty($tmpstr)) $tmpstr = base::getLRStr($tmpstr, ',', 'leftr');
      return $tmpstr;
    }

    public static function getCategorySelectByGenre($argGenre, $argLang, $argMyCategory, $argVars = '')
    {
      $tmpstr = '';
      $genre = $argGenre;
      $lang = base::getNum($argLang, 0);
      $myCategory = $argMyCategory;
      $vars = $argVars;
      $id = base::getNum(base::getParameter($vars, 'id'), 0);
      $fid = base::getNum(base::getParameter($vars, 'fid'), 0);
      $rank = base::getNum(base::getParameter($vars, 'rank'), -1);
      $categoryAry = self::getCategoryAryByGenre($genre, $lang);
      if (is_array($categoryAry))
      {
        $rank += 1;
        $prefix = self::getPrefix();
        foreach ($categoryAry as $key => $val)
        {
          if (is_array($val))
          {
            $rsid = base::getNum($val[$prefix . 'id'], 0);
            $rsfid = base::getNum($val[$prefix . 'fid'], -1);
            if ($rsfid == $fid && (base::isEmpty($myCategory) || base::checkInstr($myCategory, $rsid)))
            {
              $explain = base::getRepeatedString(tpl::take('global.config.spstr', 'lng'), $rank) . base::htmlEncode($val[$prefix . 'topic']);
              if ($rsid == $id) $tmpstr .= tpl::take('global.config.xmlselect_select', 'tpl', 0, array('explain' => $explain, 'value' => $rsid));
              else $tmpstr .= tpl::take('global.config.xmlselect_unselect', 'tpl', 0, array('explain' => $explain, 'value' => $rsid));
              $tmpstr .= self::getCategorySelectByGenre($genre, $lang, $myCategory, 'id=' . $id . ';fid=' . $rsid . ';rank=' . $rank);
            }
          }
        }
      }
      return $tmpstr;
    }

    public static function getCategoryBreadcrumbByID($argGenre, $argLang, $argID)
    {
      $tmpstr = '';
      $genre = $argGenre;
      $lang = base::getNum($argLang, 0);
      $id = base::getNum($argID, 0);
      $categoryAry = self::getCategoryAryByGenre($genre, $lang);
      if (is_array($categoryAry))
      {
        $prefix = self::getPrefix();
        $baseHTML = tpl::take('global.config.breadcrumb', 'tpl');
        $baseArrowHTML = tpl::take('global.config.breadcrumb-arrow', 'tpl');
        foreach ($categoryAry as $key => $val)
        {
          if (is_array($val))
          {
            $rsid = base::getNum($val[$prefix . 'id'], 0);
            $rsfid = base::getNum($val[$prefix . 'fid'], 0);
            $rsTopic = base::getString($val[$prefix . 'topic']);
            if ($rsid == $id)
            {
              $tmpstr = $baseArrowHTML . $baseHTML;
              $tmpstr = str_replace('{$text}', base::htmlEncode($rsTopic), $tmpstr);
              $tmpstr = str_replace('{$link}', base::htmlEncode(smart::createURL('list', $rsid, null, $genre)), $tmpstr);
              if ($rsfid != 0) $tmpstr = self::getCategoryBreadcrumbByID($genre, $lang, $rsfid) . $tmpstr;
            }
          }
        }
      }
      return $tmpstr;
    }

    public static function getCategoryNavByID($argGenre, $argLang, $argID)
    {
      $tmpstr = '';
      $genre = $argGenre;
      $lang = base::getNum($argLang, 0);
      $id = base::getNum($argID, 0);
      $categoryAry = self::getCategoryAryByGenre($genre, $lang);
      if (is_array($categoryAry))
      {
        $prefix = self::getPrefix();
        foreach ($categoryAry as $key => $val)
        {
          if (is_array($val))
          {
            $rsid = base::getNum($val[$prefix . 'id'], 0);
            $rsfid = base::getNum($val[$prefix . 'fid'], 0);
            $rsTopic = base::getString($val[$prefix . 'topic']);
            if ($rsid == $id)
            {
              $tmpstr = tpl::take('::console.link-nav', 'tpl', 0, array('text' => base::htmlEncode($rsTopic), 'link' => base::htmlEncode('?type=list&category=' . $rsid)));
              if ($rsfid != 0) $tmpstr = self::getCategoryNavByID($genre, $lang, $rsfid) . $tmpstr;
            }
          }
        }
      }
      return $tmpstr;
    }

    public static function getCategoryTopicByID($argGenre, $argLang, $argID)
    {
      $tmpstr = '';
      $genre = $argGenre;
      $lang = base::getNum($argLang, 0);
      $id = base::getNum($argID, 0);
      $categoryAry = self::getCategoryAryByGenre($genre, $lang);
      if (is_array($categoryAry))
      {
        $prefix = self::getPrefix();
        foreach ($categoryAry as $key => $val)
        {
          if (is_array($val))
          {
            $rsid = base::getNum($val[$prefix . 'id'], 0);
            if ($rsid == $id) $tmpstr = $val[$prefix . 'topic'];
          }
        }
      }
      return $tmpstr;
    }

    public static function getDBCategoryAryByGenre($argGenre, $argLang, $argFid = 0)
    {
      $genre = $argGenre;
      $lang = base::getNum($argLang, 0);
      $fid = base::getNum($argFid, 0);
      $categoryAry = array();
      $db = page::db();
      $table = tpl::take('global.universal/category:config.db_table', 'cfg');
      $prefix = tpl::take('global.universal/category:config.db_prefix', 'cfg');
      if (!is_null($db) && !base::isEmpty($table))
      {
        $sqlstr = "select * from " . $table . " where " . $prefix . "lang=" . $lang . " and " . $prefix . "delete=0 and " . $prefix . "fid=" . $fid . " and " . $prefix . "genre='" . addslashes($genre) . "' order by " . $prefix . "order asc," . $prefix . "id asc";
        $rq = $db -> query($sqlstr);
        while($rs = $rq -> fetch())
        {
          $rsid = base::getNum($rs[$prefix . 'id'], 0);
          $categoryAry['id' . $rsid] = $rs;
          $categoryAry = array_merge($categoryAry, self::getDBCategoryAryByGenre($genre, $lang, $rsid));
        }
      }
      return $categoryAry;
    }

    public static function getPrefix()
    {
      $tmpstr = tpl::take('global.universal/category:config.db_prefix', 'cfg');
      return $tmpstr;
    }
  }
}
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
?>
