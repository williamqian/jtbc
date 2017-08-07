<?php
header('content-type: text/xml; charset=utf-8');
require_once('../common/incfiles/page.inc.php');
require_once('common/incfiles/install.inc.php');
echo jtbc\ui::getResult();
?>
