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
    $id = base::getNum(request::getHTTPPara('id', 'get'), 0);
    $title = request::getHTTPPara('title', 'post');
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
        $bool = tpl::bring('index.title', 'lng', request::getHTTPPara('title', 'post'), $langText);
        if ($bool) $bool = tpl::bring('index.content', 'lng', request::getHTTPPara('content', 'post'), $langText);
        if ($bool) $bool = tpl::bring('index.att', 'lng', request::getHTTPPara('att', 'post'), $langText);
        if ($bool)
        {
          $status = 1;
          $message = tpl::take('manage.text-tips-edit-done', 'lng');
          $account -> creatAutoLog('manage.log-edit-1');
        }
      }
    }
    if (count($error) != 0) $message = implode('|', $error);
    $tmpstr = self::formatMsgResult($status, $message);
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
            $account -> creatAutoLog('manage.log-upload-1', array('filepath' => $paraArray['filepath']));
          }
        }
      }
    }
    $tmpstr = self::formatMsgResult($status, $message, $para);
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
