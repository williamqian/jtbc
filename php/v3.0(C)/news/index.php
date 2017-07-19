<?php
require_once('../common/incfiles/page.inc.php');
require_once('common/incfiles/index.inc.php');
header('content-type: text/html; charset=' . CHARSET);
echo jtbc\ui::getResult();
?>