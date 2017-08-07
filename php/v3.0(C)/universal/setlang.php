<?php
header('content-type: text/html; charset=utf-8');
require_once('../common/incfiles/page.inc.php');
require_once('common/incfiles/setlang.inc.php');
header('location: ' . jtbc\ui::getRedirect());
?>
