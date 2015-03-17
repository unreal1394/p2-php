<?php
/**
 * rep2 - スレッド表示スクリプト（実況専用）
 * フレーム分割画面、右下部分
 */

define('P2_SESSION_CLOSE_AFTER_AUTHENTICATION', 0);
require_once __DIR__ . '/../init.php';

$_login->authorize(); // ユーザ認証

// +Wiki
require_once P2_LIB_DIR . '/wiki/read.inc.php';

// iPhone
if ($_conf['iphone']) {
    include P2_LIB_DIR . '/toolbar_i.inc.php';
    define('READ_HEADER_INC_PHP', P2_LIB_DIR . '/read_header_i.inc.php');
    define('READ_FOOTER_INC_PHP', P2_LIB_DIR . '/read_footer_i.inc.php');
// 携帯
} elseif ($_conf['ktai']) {
    define('READ_HEADER_INC_PHP', P2_LIB_DIR . '/read_header_k.inc.php');
    define('READ_FOOTER_INC_PHP', P2_LIB_DIR . '/read_footer_k.inc.php');
// PC
} else {
    define('READ_HEADER_INC_PHP', P2_LIB_DIR . '/read_header.inc.php');
    define('READ_FOOTER_INC_PHP', P2_LIB_DIR . '/read_footer.inc.php');
}

//================================================================
// 変数
//================================================================
$newtime = date('gis');  // 同じリンクをクリックしても再読込しない仕様に対抗するダミークエリー
// $_today = date('y/m/d');
$is_ajax = !empty($_GET['ajax']);

//=================================================
// スレの指定
//=================================================
detectThread();    // global $host, $bbs, $key, $ls

//=================================================
// レスフィルタ
//=================================================
$do_filtering = false;
if (array_key_exists('rf', $_REQUEST) && is_array($_REQUEST['rf'])) {
    $resFilter = ResFilter::configure($_REQUEST['rf']);
    if ($resFilter->hasWord()) {
        $do_filtering = true;
        if ($_conf['ktai']) {
            $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
            $resFilter->setRange($_conf['mobile.rnum_range'], $page);
        }
        if (empty($popup_filter) && isset($_REQUEST['submit_filter'])) {
            $resFilter->save();
        }
    }
} else {
    $resFilter = ResFilter::restore();
}

//=================================================
// あぼーん&NGワード設定読み込み
//=================================================
$GLOBALS['ngaborns'] = NgAbornCtl::loadNgAborns();

//==================================================================
// メイン
//==================================================================

if (!isset($aThread)) {
    $aThread = new ThreadRead();
}

// lsのセット
if (!empty($ls)) {
    $aThread->ls = mb_convert_kana($ls, 'a');
}

//==========================================================
// idxの読み込み
//==========================================================

// hostを分解してidxファイルのパスを求める
if (!isset($aThread->keyidx)) {
    $aThread->setThreadPathInfo($host, $bbs, $key);
}

// 板ディレクトリが無ければ作る
FileCtl::mkdirFor($aThread->keyidx);
FileCtl::mkdirFor($aThread->keydat);

$aThread->itaj = P2Util::getItaName($host, $bbs);
if (!$aThread->itaj) { $aThread->itaj = $aThread->bbs; }

// idxファイルがあれば読み込む
if ($lines = FileCtl::file_read_lines($aThread->keyidx, FILE_IGNORE_NEW_LINES)) {
    $idx_data = explode('<>', $lines[0]);
} else {
    $idx_data = array_fill(0, 12, '');
}
$aThread->getThreadInfoFromIdx();

//===========================================================
// DATのダウンロード
//===========================================================
$offline = !empty($_GET['offline']);

if (!$offline) {
    $aThread->downloadDat();
}

// DATを読み込み
$aThread->readDat();

// オフライン指定でもログがなければ、改めて強制読み込み
if (empty($aThread->datlines) && $offline) {
    $aThread->downloadDat();
    $aThread->readDat();
}

// タイトルを取得して設定
$aThread->setTitleFromLocal();

//===========================================================
// 表示レス番の範囲を設定
//===========================================================
if ($_conf['ktai']) {
    $before_respointer = $_conf['mobile.before_respointer'];
} else {
    $before_respointer = $_conf['before_respointer'];
}

// 取得済みなら
if ($aThread->isKitoku()) {

    //「新着レスの表示」の時は特別にちょっと前のレスから表示
    if (!empty($_GET['nt'])) {
        if (substr($aThread->ls, -1) == '-') {
            $n = $aThread->ls - $before_respointer;
            if ($n < 1) { $n = 1; }
            $aThread->ls = $n . '-';
        }

    } elseif (!$aThread->ls) {
        $from_num = $aThread->readnum +1 - $_conf['respointer'] - $before_respointer;
        if ($from_num < 1) {
            $from_num = 1;
        } elseif ($from_num > $aThread->rescount) {
            $from_num = $aThread->rescount - $_conf['respointer'] - $before_respointer;
        }
        $aThread->ls = $from_num . '-';
    }

    if ($_conf['ktai'] && strpos($aThread->ls, 'n') === false) {
        $aThread->ls = $aThread->ls . 'n';
    }

// 未取得なら
} else {
    if (!$aThread->ls) {
        $aThread->ls = $_conf['get_new_res_l'];
    }
}

// フィルタリングの時は、all固定とする
if ($resFilter && $resFilter->hasWord()) {
    $aThread->ls = 'all';
}

$aThread->lsToPoint();

//===============================================================
// プリント
//===============================================================
$ptitle_ht = p2h($aThread->itaj) . ' / ' . $aThread->ttitle_hd;

if ($_conf['ktai']) {

    if ($resFilter && $resFilter->hasWord() && $aThread->rescount) {
        $GLOBALS['filter_hits'] = 0;
    } else {
        $GLOBALS['filter_hits'] = null;
    }

    $aShowThread = new ShowThreadK($aThread);

    if ($is_ajax) {
        $response = trim(mb_convert_encoding($aShowThread->getDatToHtml(true), 'UTF-8', 'CP932'));
        if (isset($_GET['respop_id'])) {
            $response = preg_replace('/<[^<>]+? id="/u', sprintf('$0_respop%d_', $_GET['respop_id']), $response);
        }
        /*if ($_conf['iphone']) {
            // HTMLの断片をXMLとして渡してもDOMでidやclassが期待通りに反映されない
            header('Content-Type: application/xml; charset=UTF-8');
            //$responseId = 'ajaxResponse' . time();
            $doc = new DOMDocument();
            $err = error_reporting(E_ALL & ~E_WARNING);
            $html = '<html><head>'
                  . '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">'
                  . '</head><body>'
                  . $response
                  . '</body></html>';
            $doc->loadHTML($html);
            error_reporting($err);
            echo '<?xml version="1.0" encoding="utf-8" ?>';
            echo $doc->saveXML($doc->getElementsByTagName('div')->item(0));
        } else {*/
            // よって、HTMLの断片をそのまま返してinnterHTMLに代入しないといけない。
            // (根本的にレスポンスのフォーマットとクライアント側での処理を変えない限りは)
            header('Content-Type: text/html; charset=UTF-8');
            echo $response;
        //}
    } else {
        if ($aThread->rescount) {
            if ($_GET['showbl']) {
                $content = $aShowThread->getDatToHtml_resFrom();
            } else {
                $content = $aShowThread->getDatToHtml();
            }
        } elseif ($aThread->diedat && count($aThread->datochi_residuums) > 0) {
            $content = $aShowThread->getDatochiResiduums();
        }

        include READ_HEADER_INC_PHP;

        if ($_conf['iphone'] && $_conf['expack.spm.enabled']) {
            echo $aShowThread->getSpmObjJs();
        }

        echo $content;

        include READ_FOOTER_INC_PHP;
    }

} else {

    // ヘッダ 表示
    include READ_HEADER_INC_PHP;
    flush();

    //===========================================================
    // ローカルDatを変換してHTML表示
    //===========================================================
    // レスがあり、検索指定があれば
    if ($resFilter && $resFilter->hasWord() && $aThread->rescount) {

        $all = $aThread->rescount;

        $GLOBALS['filter_hits'] = 0;

        echo "<p><b id=\"filterstart\">{$all}レス中 <span id=\"searching\">n</span>レスがヒット</b></p>\n";
    }
    if ($_GET['showbl']) {
        echo  '<p><b>' . p2h($aThread->resrange['start']) . 'へのレス</b></p>';
    }

    //$GLOBALS['debug'] && $GLOBALS['profiler']->enterSection("datToHtml");

    if ($aThread->rescount) {
        echo '<div id="live_view"></div>';

    } elseif ($aThread->diedat && count($aThread->datochi_residuums) > 0) {
        echo '過去ログ又はDATを取得出来ないスレッドは実況できません';
    }

    //$GLOBALS['debug'] && $GLOBALS['profiler']->leaveSection("datToHtml");

    // フィルタ結果を表示
    if ($resFilter && $resFilter->hasWord() && $aThread->rescount) {
        echo <<<EOP
<script type="text/javascript">
//<![CDATA[
var filterstart = document.getElementById('filterstart');
if (filterstart) {
    filterstart.style.backgroundColor = 'yellow';
    filterstart.style.fontWeight = 'bold';
}
//]]>
</script>\n
EOP;
        if ($GLOBALS['filter_hits'] > 5) {
            echo "<p><b class=\"filtering\">{$all}レス中 {$GLOBALS['filter_hits']}レスがヒット</b></p>\n";
        }
    }

    // フッタ 表示
    include READ_FOOTER_INC_PHP;
}
flush();

// 以上 ---------------------------------------------------------------
exit;

//===============================================================================
// 関数
//===============================================================================
// {{{ detectThread()

/**
 * スレッドを指定する
 */
function detectThread()
{
    global $_conf, $host, $bbs, $key, $ls;

    list($nama_url, $host, $bbs, $key, $ls) = P2Util::detectThread();

    if (!($host && $bbs && $key)) {
        if ($nama_url) {
            $nama_url = p2h($nama_url);
            p2die('スレッドの指定が変です。', "<a href=\"{$nama_url}\">{$nama_url}</a>", true);
        } else {
            p2die('スレッドの指定が変です。');
        }
    }
}

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
