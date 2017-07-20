<?php
namespace jtbc;
class ui extends page {
  public static function moduleActionAdd()
  {
    $tmpstr = '';
    $status = 0;
    $message = '';
    $error = array();
    $name = request::getHTTPPara('name', 'post');
    $mobile = request::getHTTPPara('mobile', 'post');
    $email = request::getHTTPPara('email', 'post');
    $content = request::getHTTPPara('content', 'post');
    if (base::isEmpty($name)) array_push($error, tpl::take('api.text-tips-add-error-1', 'lng'));
    if (!verify::isMobile($mobile)) array_push($error, tpl::take('api.text-tips-add-error-2', 'lng'));
    if (!verify::isEmail($email)) array_push($error, tpl::take('api.text-tips-add-error-3', 'lng'));
    if (base::isEmpty($content)) array_push($error, tpl::take('api.text-tips-add-error-4', 'lng'));
    if (count($error) == 0)
    {
      $db = self::db();
      if (!is_null($db))
      {
        $table = tpl::take('config.db_table', 'cfg');
        $prefix = tpl::take('config.db_prefix', 'cfg');
        $specialFiled = $prefix . 'id,' . $prefix . 'dispose,' . $prefix . 'delete';
        $preset = array();
        $preset[$prefix . 'userip'] = request::getRemortIP();
        $preset[$prefix . 'lang'] = smart::getForeLang();
        $preset[$prefix . 'time'] = base::getDateTime();
        $sqlstr = smart::getAutoRequestInsertSQL($table, $specialFiled, $preset);
        $re = $db -> exec($sqlstr);
        if (is_numeric($re))
        {
          $status = 1;
          $message = tpl::take('api.text-tips-add-done', 'lng');
        }
        else array_push($error, tpl::take('api.text-tips-add-error-others', 'lng'));
      }
      else array_push($error, tpl::take('api.text-tips-add-error-others', 'lng'));
    }
    if (count($error) != 0) $message = implode('|', $error);
    $tmpstr = self::formatMsgResult($status, $message);
    return $tmpstr;
  }
}
?>
