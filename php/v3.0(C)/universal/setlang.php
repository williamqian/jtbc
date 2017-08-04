<?php
require_once('../common/incfiles/page.inc.php');
require_once('common/incfiles/setlang.inc.php');
header('content-type: text/html; charset=' . CHARSET);
header('location: ' . jtbc\ui::getRedirect());
?>