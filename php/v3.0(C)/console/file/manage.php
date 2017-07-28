<?php
header('cache-control: no-cache, must-revalidate');
header('content-type: text/xml; charset=' . CHARSET);
require_once('../../common/incfiles/page.inc.php');
require_once('common/incfiles/manage.inc.php');
echo jtbc\ui::getResult();
?>