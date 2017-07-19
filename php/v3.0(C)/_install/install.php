<?php
require_once('../common/incfiles/page.inc.php');
require_once('common/incfiles/install.inc.php');
header('content-type: text/xml; charset=' . CHARSET);
echo jtbc\ui::getResult();
?>