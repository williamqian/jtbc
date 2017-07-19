<?php
require_once('../../common/incfiles/page.inc.php');
require_once('common/incfiles/managerapi.inc.php');
header('cache-control: no-cache, must-revalidate');
header('content-type: text/xml; charset=' . CHARSET);
echo jtbc\ui::getResult();
?>