<?php
namespace jtbc;
class ui extends page {
  public static $account = null;

  public static function account()
  {
    $account = null;
    if (!is_null(self::$account)) $account = self::$account;
    else $account = self::$account = new console\account();
    return $account;
  }

  protected static function ppGetPathAryBySymbol($argSymbol)
  {
    $pathAry = array();
    $symbol = $argSymbol;
    if (!base::isEmpty($symbol))
    {
      $symbolAry = explode('.', $symbol);
      if (count($symbolAry) == 3)
      {
        $filepath = smart::getActualRoute($symbolAry[0], 1);
        if (base::getRight($filepath, 1) != '/') $filepath .= '/';
        switch($symbolAry[1])
        {
          case 'cfg':
            $filepath .= 'common/';
            break;
          case 'lng':
            $filepath .= 'common/language/';
            break;
          case 'tpl':
            $filepath .= 'common/template/';
            break;
          default:
            $filepath .= 'common/';
            break;
        }
        $filepath .= $symbolAry[2] . XMLSFX;
        $pathAry['filepath'] = $filepath;
        $pathAry['activevalue'] = tpl::getActiveValue($symbolAry[1]);
      }
    }
    return $pathAry;
  }

  public static function moduleList()
  {
    $status = 1;
    $tmpstr = '';
    $currentNode = '';
    $currentValue = '';
    $node = base::getString(request::getHTTPPara('node', 'get'));
    $symbol = base::getString(request::getHTTPPara('symbol', 'get'));
    if (base::isEmpty($symbol)) $symbol = '.tpl.index';
    $account = self::account();
    $tmpstr = tpl::take('manage.list-disabled', 'tpl');
    $pathAry = self::ppGetPathAryBySymbol($symbol);
    if (!empty($pathAry))
    {
      $nodeIndex = 0;
      $filepath = $pathAry['filepath'];
      $tplActiveValue = $pathAry['activevalue'];
      $xmlAry = tpl::getXMLInfo($filepath, $tplActiveValue);
      if (!empty($xmlAry))
      {
        $tmpstr = tpl::take('manage.list', 'tpl');
        $tpl = new tpl();
        $tpl -> tplString = $tmpstr;
        $loopString = $tpl -> getLoopString('{@}');
        foreach ($xmlAry as $key => $val)
        {
          if ($nodeIndex == 0)
          {
            $currentNode = $key;
            $currentValue = $val;
          }
          if ($key == $node)
          {
            $currentNode = $key;
            $currentValue = $val;
          }
          $loopLineString = $loopString;
          $loopLineString = str_replace('{$key}', base::htmlEncode($key), $loopLineString);
          $tpl -> insertLoopLine($loopLineString);
          $nodeIndex += 1;
        }
        $tmpstr = $tpl -> mergeTemplate();
        $tmpstr = str_replace('{$-symbol}', base::htmlEncode($symbol), $tmpstr);
        $tmpstr = str_replace('{$-filepath}', base::htmlEncode($filepath), $tmpstr);
        $tmpstr = str_replace('{$-current-key}', base::htmlEncode($currentNode), $tmpstr);
        $tmpstr = str_replace('{$-current-val}', base::htmlEncode($currentValue), $tmpstr);
      }
    }
    $tmpstr = tpl::parse($tmpstr);
    $tmpstr = $account -> replaceAccountTag($tmpstr);
    $tmpstr = self::formatResult($status, $tmpstr);
    return $tmpstr;
  }

  public static function moduleActionEdit()
  {
    $tmpstr = '';
    $status = 0;
    $message = '';
    $error = array();
    $account = self::account();
    $node = base::getString(request::getHTTPPara('node', 'post'));
    $symbol = base::getString(request::getHTTPPara('symbol', 'post'));
    $content = base::getString(request::getHTTPPara('content', 'post'));
    if (!$account -> checkPopedom(self::getPara('genre'), 'edit'))
    {
      array_push($error, tpl::take('::console.text-tips-error-403', 'lng'));
    }
    else
    {
      $pathAry = self::ppGetPathAryBySymbol($symbol);
      if (!empty($pathAry))
      {
        $filepath = $pathAry['filepath'];
        $tplActiveValue = $pathAry['activevalue'];
        $bool = tpl::setXMLInfo($filepath, $tplActiveValue, $node, $content);
        if ($bool == false) array_push($error, tpl::take('manage.text-tips-edit-error-1', 'lng'));
        else
        {
          $status = 1;
          $message = tpl::take('manage.text-tips-edit-done', 'lng');
          $logString = tpl::take('manage.log-edit-1', 'lng');
          $logString = str_replace('{$symbol}', $symbol, $logString);
          $logString = str_replace('{$node}', $node, $logString);
          $account -> creatLog(self::getPara('genre'), $logString, request::getRemortIP());
        }
      }
      else array_push($error, tpl::take('manage.text-tips-edit-error-1', 'lng'));
    }
    if (count($error) != 0) $message = implode('|', $error);
    $tmpstr = self::formatMsgResult($status, $message);
    return $tmpstr;
  }

  public static function getResult()
  {
    $tmpstr = '';
    $account = self::account();
    if ($account -> checkLogin())
    {
      if ($account -> checkPopedom(self::getPara('genre')))
      {
        $tmpstr = parent::getResult();
      }
    }
    return $tmpstr;
  }
}
?>
