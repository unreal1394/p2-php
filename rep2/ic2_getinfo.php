<?php
/**
 * ImageCache2 - 画像のID or URLから情報を取得する
 */

// {{{ p2基本設定読み込み&認証

require_once __DIR__ . '/../init.php';

$_login->authorize();

if (!$_conf['expack.ic2.enabled']) {
    p2die('ImageCache2は無効です。', 'conf/conf_admin_ex.inc.php の設定を変えてください。');
}

// }}}
// {{{ HTTPヘッダ

P2Util::header_nocache();
header('Content-Type: application/json; charset=UTF-8');

// }}}
// {{{ 初期化

// パラメータを検証
if (!isset($_GET['id']) && !isset($_GET['url']) && !isset($_GET['md5'])) {
    echo 'null';
    exit;
}

// ライブラリ読み込み
require_once P2EX_LIB_DIR . '/ImageCache2/bootstrap.php';

// }}}
// {{{ execute

$icdb = new ImageCache2_DataObject_Images();
if (isset($_GET['id'])) {
    $icdb->whereAdd(sprintf('id=%d', (int)$_GET['id']));
} elseif (isset($_GET['url'])) {
    $icdb->whereAddQuoted('uri', '=', (string)$_GET['url']);
} else {
    $icdb->whereAddQuoted('md5', '=', (string)$_GET['md5']);
}

if (!$icdb->find(1)) {
    echo 'null';
    exit;
}

$thumb_type = isset($_GET['t']) ? $_GET['t'] : ImageCache2_Thumbnailer::SIZE_DEFAULT;
switch ($thumb_type) {
    case ImageCache2_Thumbnailer::SIZE_PC:
    case ImageCache2_Thumbnailer::SIZE_MOBILE:
    case ImageCache2_Thumbnailer::SIZE_INTERMD:
        $thumbnailer = new ImageCache2_Thumbnailer($thumb_type);
        break;
    default:
        $thumbnailer = new ImageCache2_Thumbnailer();
}

$size = (int)$icdb->size;
$md5  = $icdb->md5;
$mime = $icdb->mime;

$srcPath   = $thumbnailer->srcPath($size, $md5, $mime);
$thumbPath = $thumbnailer->thumbPath($size, $md5, $mime);
$srcUrl    = $thumbnailer->srcUrl($size, $md5, $mime);
$thumbUrl  = $thumbnailer->thumbUrl($size, $md5, $mime);

echo json_encode(array(
    'id'     => (int)$icdb->id,
    'uri'    => $icdb->uri,
    'host'   => $icdb->host,
    'name'   => $icdb->name,
    'size'   => $size,
    'md5'    => $md5,
    'width'  => (int)$icdb->width,
    'height' => (int)$icdb->height,
    'mime'   => $mime,
    'rank'   => (int)$icdb->rank,
    'time'   => (int)$icdb->time,
    'memo'   => $icdb->memo,
    'url'    => $icdb->uri,
    'src'    => ($srcPath && file_exists($srcPath)) ? $srcUrl : null,
    'thumb'  => ($thumbPath && file_exists($thumbPath)) ? $thumbUrl : null,
));
exit;

// }}}

/*
 * Local Variables:
 * mode: php
 * coding: cp932
 * tab-width: 4
 * c-basic-offset: 4
 * indent-tabs-mode: nil
 * End:
 */
// vim: set syn=php fenc=cp932 ai et ts=4 sw=4 sts=4 fdm=marker:
