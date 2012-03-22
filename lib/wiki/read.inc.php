<?php
/*
    p2 - スレッド表示でincludeされるファイル
*/

if (!$_conf['ktai']) {
    // リンクプラグイン
    require_once P2_LIB_DIR . '/wiki/LinkPluginCtl.php';
    $GLOBALS['linkPluginCtl'] = new LinkPluginCtl();
    // 置換画像URL(PCでImageCache2が有効の場合)
    if ($_conf['expack.ic2.enabled'] % 2 == 1) {
        require_once P2_LIB_DIR . '/wiki/ReplaceImageUrlCtl.php';
        $GLOBALS['replaceImageUrlCtl'] = new ReplaceImageUrlCtl();
    }
} else {
    // 置換画像URL(携帯でImageCache2が有効の場合)
    if ($_conf['expack.ic2.enabled'] >= 2) {
        require_once P2_LIB_DIR . '/wiki/ReplaceImageUrlCtl.php';
        $GLOBALS['replaceImageUrlCtl'] = new ReplaceImageUrlCtl();
    }
}
// 置換ワード
require_once P2_LIB_DIR . '/wiki/replacewordctl.class.php';
$GLOBALS['replaceWordCtl'] = new ReplaceWordCtl();
