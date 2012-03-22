<?php
if ($_conf['wiki.ng_thread']) {
    require_once P2_LIB_DIR . '/wiki/NgThreadCtl.php';
    $GLOBALS['ngThreadCtl'] = new NgThreadCtl();
}
