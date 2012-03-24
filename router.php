<?php
// router.php
if (preg_match('@^(/js/\\w+\\.js)(\\?\\d+)?$@', $_SERVER['REQUEST_URI'], $matches)) {
    header('Content-Type: text/javascript; charset=Shift_JIS');
    readfile($_SERVER['DOCUMENT_ROOT'] . $matches[1]);
} else {
    return false;
}
