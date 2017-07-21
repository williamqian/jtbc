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

  public static function moduleDashbord()
  {
    $status = 1;
    $tmpstr = '';
    $account = self::account();
    if ($account -> checkLogin())
    {
      $tmpstr = tpl::take('manage.dashbord', 'tpl');
      $hello = tpl::take('manage.text-hello', 'lng');
      $hello = str_replace('{$-username}', $account -> getMyInfo('username'), $hello);
      $hello = str_replace('{$-lastip}', $account -> getMyInfo('lastip'), $hello);
      $hello = str_replace('{$-lasttime}', $account -> getMyInfo('lasttime'), $hello);
      $hello = str_replace('{$-role-topic}', $account -> getRoleTopicById($account -> getMyInfo('role')), $hello);
      $tmpstr = str_replace('{$-hello}', base::htmlEncode($hello), $tmpstr);
      $tmpstr = str_replace('{$-sys-para-0}', base::htmlEncode($_SERVER['SERVER_SOFTWARE']), $tmpstr);
      $tmpstr = str_replace('{$-sys-para-1}', base::htmlEncode(VERSION), $tmpstr);
      $tmpstr = str_replace('{$-sys-para-2}', base::htmlEncode(PHP_VERSION), $tmpstr);
      $tmpstr = str_replace('{$-sys-para-3}', base::htmlEncode(DB), $tmpstr);
      $tmpstr = str_replace('{$-sys-para-4}', base::htmlEncode(strtoupper(php_sapi_name())), $tmpstr);
      $tmpstr = str_replace('{$-sys-para-5}', base::htmlEncode(gethostbyname($_SERVER['SERVER_NAME'])), $tmpstr);
      $tmpstr = str_replace('{$-sys-para-6}', base::htmlEncode(base::formatDate(base::getDateTime(), '100')), $tmpstr);
      $tmpstr = str_replace('{$-sys-para-7}', base::htmlEncode(get_cfg_var('max_execution_time')), $tmpstr);
      $tmpstr = str_replace('{$-sys-para-8}', base::htmlEncode(get_cfg_var('post_max_size')), $tmpstr);
      $tmpstr = str_replace('{$-sys-para-9}', base::htmlEncode(get_cfg_var('upload_max_filesize')), $tmpstr);
      $tmpstr = str_replace('{$-sys-para-10}', base::htmlEncode(get_cfg_var('memory_limit')), $tmpstr);
      $tmpstr = $account -> replaceAccountTag($tmpstr);
      $tmpstr = tpl::parse($tmpstr);
    }
    $tmpstr = self::formatResult($status, $tmpstr);
    return $tmpstr;
  }

  public static function moduleModifyPassword()
  {
    $status = 1;
    $tmpstr = '';
    $account = self::account();
    if ($account -> checkLogin())
    {
      $tmpstr = tpl::take('manage.modifypassword', 'tpl');
      $tmpstr = tpl::parse($tmpstr);
    }
    $tmpstr = self::formatResult($status, $tmpstr);
    return $tmpstr;
  }

  public static function moduleGetLang()
  {
    $status = 1;
    $tmpstr = '';
    $account = self::account();
    if ($account -> checkLogin())
    {
      $lang = $account -> getLang();
      $myRoleLang = base::getString($account -> getMyInfo('lang', 'role'));
      $allLangAry = tpl::take('::sel_lang.*', 'lng');
      $currentLang = tpl::take('::sel_lang.' . $lang, 'lng');
      $tmpstr = tpl::take('manage.getlang', 'tpl');
      $tpl = new tpl();
      $tpl -> tplString = $tmpstr;
      $loopString = $tpl -> getLoopString('{@}');
      foreach ($allLangAry as $key => $val)
      {
        if ($account -> checkIsSuper() || base::checkInstr($myRoleLang, $key))
        {
          $loopLineString = $loopString;
          $loopLineString = str_replace('{$-lang-val}', base::htmlEncode($key), $loopLineString);
          $loopLineString = str_replace('{$-lang-text}', base::htmlEncode($val), $loopLineString);
          $tpl -> insertLoopLine($loopLineString);
        }
      }
      $tmpstr = $tpl -> mergeTemplate();
      $tmpstr = str_replace('{$-current-lang-val}', base::htmlEncode($lang), $tmpstr);
      $tmpstr = str_replace('{$-current-lang-text}', base::htmlEncode($currentLang), $tmpstr);
      $tmpstr = tpl::parse($tmpstr);
    }
    $tmpstr = self::formatResult($status, $tmpstr);
    return $tmpstr;
  }

  public static function moduleDefault()
  {
    $status = 1;
    $tmpstr = '';
    $account = self::account();
    if (!$account -> checkLogin())
    {
      $db = self::db();
      $tmpstr = tpl::take('manage.login', 'tpl');
      $tmpstr = str_replace('{$-db-error}', is_null($db)? '1': '0', $tmpstr);
      $tmpstr = tpl::parse($tmpstr);
    }
    else
    {
      $tmpstr = tpl::take('manage.console', 'tpl');
      $tmpstr = str_replace('{$-account-username}', base::htmlEncode($account -> getMyInfo('username')), $tmpstr);
      $tmpstr = str_replace('{$-account-leftmenu}', $account -> getMyConsoleMenu(tpl::take('manage.part-leftmenu', 'tpl'), tpl::take('manage.part-leftmenu-dl', 'tpl')), $tmpstr);
      $tmpstr = tpl::parse($tmpstr);
    }
    $tmpstr = self::formatResult($status, $tmpstr);
    return $tmpstr;
  }

  public static function moduleActionLogin()
  {
    $tmpstr = '';
    $status = 0;
    $message = '';
    $account = self::account();
    $username = request::getHTTPPara('username');
    $password = request::getHTTPPara('password');
    $remember = request::getHTTPPara('remember');
    if (!$account -> checkLoginErrorMax($username))
    {
      if ($account -> checkLoginInfo($username, $password))
      {
        $status = 1;
        $cookiesExpireTime = 0;
        if ($remember == '1') $cookiesExpireTime = time() + 31536000;
        setcookie(APPNAME . 'console[username]', $username, $cookiesExpireTime, COOKIESPATH);
        setcookie(APPNAME . 'console[authentication]', md5(WEBKEY . md5($password)), $cookiesExpireTime, COOKIESPATH);
        $logString = tpl::take('manage.log-login-1', 'lng');
        $account -> creatLog(self::getPara('genre'), $logString, request::getRemortIP());
      }
      else $message = tpl::take('manage.msg-login-1', 'lng');
    }
    else
    {
      $message = tpl::take('manage.msg-login-2', 'lng');
      $message = str_replace('{$num}', tpl::take('config.login-error-max', 'cfg'), $message);
    }
    $tmpstr = self::formatMsgResult($status, $message);
    return $tmpstr;
  }

  public static function moduleActionLogout()
  {
    $tmpstr = '';
    $status = 1;
    $message = '';
    $account = self::account();
    $account -> logout();
    $tmpstr = self::formatMsgResult($status, $message);
    return $tmpstr;
  }

  public static function moduleActionModifyPassword()
  {
    $tmpstr = '';
    $status = 0;
    $message = '';
    $password = request::getHTTPPara('password', 'post');
    $newpassword = request::getHTTPPara('newpassword', 'post');
    $newcpassword = request::getHTTPPara('newcpassword', 'post');
    if (base::isEmpty($password)) $message = tpl::take('manage.text-modifypassword-error-1', 'lng');
    else if (base::isEmpty($newpassword)) $message = tpl::take('manage.text-modifypassword-error-2', 'lng');
    else if ($newpassword != $newcpassword) $message = tpl::take('manage.text-modifypassword-error-3', 'lng');
    else
    {
      $account = self::account();
      if ($account -> checkLogin())
      {
        if ($account -> modifyPassword($password, $newpassword))
        {
          $status = 1;
          $logString = tpl::take('manage.log-modifypassword-1', 'lng');
          $account -> creatLog(self::getPara('genre'), $logString, request::getRemortIP());
          $message = tpl::take('manage.text-modifypassword-done', 'lng');
        }
        else $message = tpl::take('manage.text-modifypassword-error-4', 'lng');
      }
      else $message = tpl::take('manage.text-modifypassword-error-4', 'lng');
    }
    $tmpstr = self::formatMsgResult($status, $message);
    return $tmpstr;
  }

  public static function moduleActionSetLang()
  {
    $tmpstr = '';
    $status = 0;
    $message = '';
    $lang = base::getNum(request::getHTTPPara('lang', 'get'), 0);
    $account = self::account();
    if ($account -> checkLogin())
    {
      if ($account -> setLang($lang)) $status = 1;
    }
    $tmpstr = self::formatMsgResult($status, $message);
    return $tmpstr;
  }
}
?>
