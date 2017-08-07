<?php
header('content-type: text/xml; charset=utf-8');
header('cache-control: no-cache, must-revalidate');
require_once('../common/incfiles/page.inc.php');
require_once('common/incfiles/manage.inc.php');
echo jtbc\ui::getResult();
?>
