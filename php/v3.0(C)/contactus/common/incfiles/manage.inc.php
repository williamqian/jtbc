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

  public static function moduleEdit()
  {
    $status = 1;
    $tmpstr = '';
    $account = self::account();
    if ($account -> checkPopedom(self::getPara('genre')))
    {
      $tmpstr = tpl::take('manage.edit', 'tpl');
      $tmpstr = str_replace('{$-lang-text}', $account -> getLangText(), $tmpstr);
      $tmpstr = tpl::parse($tmpstr);
      $tmpstr = $account -> replaceAccountTag($tmpstr);
    }
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
    $id = base::getNum(self::getHTTPPara('id', 'get'), 0);
    $title = self::getHTTPPara('title', 'post');
    if (!$account -> checkPopedom(self::getPara('genre')))
    {
      array_push($error, tpl::take('::console.text-tips-error-403', 'lng'));
    }
    else
    {
      if (base::isEmpty($title)) array_push($error, tpl::take('manage.text-tips-edit-error-1', 'lng'));
      if (count($error) == 0)
      {
        $langText = $account -> getLangText();
        $bool = tpl::bring('index.title', 'lng', self::getHTTPPara('title', 'post'), $langText);
        if ($bool) $bool = tpl::bring('index.content', 'lng', self::getHTTPPara('content', 'post'), $langText);
        if ($bool) $bool = tpl::bring('index.att', 'lng', self::getHTTPPara('att', 'post'), $langText);
        if ($bool)
        {
          $status = 1;
          $message = tpl::take('manage.text-tips-edit-done', 'lng');
          $logString = tpl::take('manage.log-edit-1', 'lng');
          $account -> creatLog(self::getPara('genre'), $logString, self::getRemortIP());
        }
      }
    }
    if (count($error) != 0) $message = implode('|', $error);
    $tmpstr = self::formatXMLResult($status, $message);
    return $tmpstr;
  }

  public static function moduleActionUpload()
  {
    $status = 0;
    $message = '';
    $para = '';
    $account = self::account();
    if (!$account -> checkPopedom(self::getPara('genre')))
    {
      $message = tpl::take('::console.text-tips-error-403', 'lng');
    }
    else
    {
      $upResult = upload::up2self(@$_FILES['file']);
      $upResultArray = json_decode($upResult, 1);
      if (is_array($upResultArray))
      {
        $status = $upResultArray['status'];
        $message = $upResultArray['message'];
        $para = $upResultArray['para'];
        if ($status == 1)
        {
          $paraArray = json_decode($para, 1);
          if (is_array($paraArray))
          {
            $logString = tpl::take('manage.log-upload-1', 'lng');
            $logString = str_replace('{$filepath}', $paraArray['filepath'], $logString);
            $account -> creatLog(self::getPara('genre'), $logString, self::getRemortIP());
          }
        }
      }
    }
    $tmpstr = self::formatXMLResult($status, $message, $para);
    return $tmpstr;
  }

  public static function moduleAction()
  {
    $tmpstr = '';
    $action = self::getHTTPPara('action', 'get');
    switch($action)
    {
      case 'edit':
        $tmpstr = self::moduleActionEdit();
        break;
      case 'upload':
        $tmpstr = self::moduleActionUpload();
        break;
    }
    return $tmpstr;
  }

  public static function getResult()
  {
    $tmpstr = '';
    $account = self::account();
    $type = self::getHTTPPara('type', 'get');
    if ($account -> checkLogin())
    {
      if ($account -> checkPopedom(self::getPara('genre')))
      {
        switch($type)
        {
          case 'edit':
            $tmpstr = self::moduleEdit();
            break;
          case 'action':
            $tmpstr = self::moduleAction();
            break;
          default:
            $tmpstr = self::moduleEdit();
            break;
        }
      }
    }
    return $tmpstr;
  }
}
?>
