<?php
/**
 * rep2 - ツールバー用ユーティリティ（iPhone）
 */

// {{{ _toolbar_i_button()

/**
 * imgタグに解像度毎の代替パスを指定するsrcset属性を生成
 *
 * @param string $icon
 * @return string
 */
function _toolbar_i_srcset($icon)
{

    $srcset = "{$icon} 1x";
    $ratios = array(2,3); //指定するpixel ratioを列挙
    foreach($ratios as $ratio) {
    //hoge.pngに対し、hoge@?x.pngが存在する時だけ(?は倍数)
	if (file_exists(str_replace(".png", "@{$ratio}x.png", $icon))==TRUE) {
	   $srcset .= ", ".str_replace(".png", "@{$ratio}x.png", $icon)." {$ratio}x";
	}
    }
    return $srcset;
}



/**
 * ツールバーボタン (リンク)
 *
 * @param string $icon
 * @param string $label
 * @param string $uri
 * @param string $attrs
 * @return string
 */
function _toolbar_i_button($icon, $label, $uri, $attrs = '')
{
    global $_conf;

    if (strlen($attrs) && strncmp($attrs, ' ', 1) !== 0) {
        $attrs = ' ' . $attrs;
    }

    if (strpos($attrs, 'class="') === false) {
        $attrs .= ' class="hoverable"';
    } else {
        $attrs = str_replace('class="', 'class="hoverable ', $attrs);
    }

    $srcset = _toolbar_i_srcset($icon);

    return <<<EOS
<span class="available"><a href="{$uri}"{$attrs}><img src="{$icon}" srcset="{$srcset}" width="48" height="32" alt="">{$label}</a></span>
EOS;
}

// }}}
/**
 * ツールバーボタン (リンク)
 *
 * @param string $icon
 * @param string $label
 * @param string $uri
 * @param string $attrs
 * @return string
 */
function toolbar_i_menuItem($icon, $label, $uri, $attrs = '')
{
    global $_conf;

    if (strlen($attrs) && strncmp($attrs, ' ', 1) !== 0) {
        $attrs = ' ' . $attrs;
    }

    $srcset = _toolbar_i_srcset($icon);

    return <<<EOS
<li><a href="{$uri}" {$attrs}><img src="{$icon}" srcset="{$srcset}" >{$label}</a></li>
EOS;
}

// }}}
// {{{ toolbar_i_disabled_button()

/**
 * 無効なツールバーボタン
 *
 * @param string $icon
 * @param string $label
 * @param string $uri
 * @return string
 */
function toolbar_i_disabled_menuItem($icon, $label)
{
    global $_conf;

    $srcset = _toolbar_i_srcset($icon);

    return <<<EOS
<li class="disabled"><a href="#" ><img src="{$icon}" srcset="{$srcset}" alt="{$label}">{$label}</a></li>
EOS;
}

// }}}
// {{{ toolbar_i_standard_button()

/**
 * 標準のツールバーボタン (リンク)
 *
 * @param string $icon
 * @param string $label
 * @param string $uri
 * @return string
 */
function toolbar_i_standard_button($icon, $label, $uri)
{
    if (strlen($uri) > 1 && strncmp($uri, '#', 1) === 0) {
        $attrs = ' onclick="return iutil.toolbarScrollTo(this, event);"';
    } else {
        $attrs = '';
    }

    if (empty($_conf['expack.iphone.toolbars.no_label']) && !empty($label) ) {
        $label = '<br>' . $label;
    } else {
        $label = '';
    }
    return _toolbar_i_button($icon, $label, $uri, $attrs);
}

// }}}
// {{{ toolbar_i_badged_button()

/**
 * バッジ付きのツールバーボタン (リンク)
 *
 * @param string $icon
 * @param string $label
 * @param string $uri
 * @param string $badge
 * @return string
 */
function toolbar_i_badged_button($icon, $label, $uri, $badge)
{
    if (empty($_conf['expack.iphone.toolbars.no_label']) && !empty($label) ) {
        $label = '<br>' . $label;
    } else {
        $label = '';
    }
    $label .= sprintf('<span class="badge l%d">%s</span>', min(strlen($badge), 4), $badge);
    return '<div id="matome">'._toolbar_i_button($icon, $label, $uri).'</div>';
}

// }}}
// {{{ toolbar_i_opentab_button()

/**
 * リンクを新しいタブで開くツールバーボタン
 *
 * @param string $icon
 * @param string $label
 * @param string $uri
 * @return string
 */
function toolbar_i_opentab_button($icon, $label, $uri)
{
    if (empty($_conf['expack.iphone.toolbars.no_label']) && !empty($label) ) {
        $label = '<br>' . $label;
    } else {
        $label = '';
    }
    return _toolbar_i_button($icon, $label, $uri, ' target="_blank"');
}

// }}}
// {{{ toolbar_i_disabled_button()

/**
 * 無効なツールバーボタン
 *
 * @param string $icon
 * @param string $label
 * @param string $uri
 * @return string
 */
function toolbar_i_disabled_button($icon, $label)
{
    global $_conf;

    if (empty($_conf['expack.iphone.toolbars.no_label']) && !empty($label)) {
        $label = '<br>' . $label;
    } else {
        $label = '';
    }

    $srcset = _toolbar_i_srcset($icon);

    return <<<EOS
<span class="unavailable"><img src="{$icon}" srcset="{$srcset}" width="48" height="32" alt="">{$label}</span>
EOS;
}

// }}}
// {{{ toolbar_i_showhide_button()

/**
 * ターゲット要素の表示・非表示をトグルするツールバーボタン
 *
 * @param string $icon
 * @param string $label
 * @param string $id
 * @return string
 */
function toolbar_i_showhide_button($icon, $label, $id)
{
    $attrs = ' onclick="return iutil.toolbarShowHide(this, event);"';
    if (empty($_conf['expack.iphone.toolbars.no_label']) && !empty($label) ) {
        $label = '<br>' . $label;
    } else {
        $label = '';
    }
    return _toolbar_i_button($icon, $label, "#{$id}", $attrs);
}

// }}}
// {{{ toolbar_i_favita_button()

/**
 * お気に板の登録・解除をトグルするツールバーボタン
 *
 * @param string $icon
 * @param string $label (fallback)
 * @param object $info @see lib/get_info.inc.php: get_board_info()
 * @param int $setnum
 * @return string
 */
function toolbar_i_favita_button($icon, $label, $info, $setnum = 0)
{
    if (!array_key_exists($setnum, $info->favs)) {
        return toolbar_i_disabled_button($icon, $label);
    }

    $fav = $info->favs[$setnum];
    $attrs = ' onclick="return iutil.toolbarRunHttpCommand(this, event);"';
    if (!$fav['set']) {
        $attrs .= ' class="inactive"';
    }
    $uri = 'httpcmd.php?' . http_build_query(array(
        'cmd'       => 'setfavita',
        'host'      => $info->host,
        'bbs'       => $info->bbs,
        'itaj_en'   => UrlSafeBase64::encode($info->itaj),
        'setnum'    => $setnum,
        'setfavita' => 2,
    ), '', '&amp;');

    if(isset($label)) // nullだったらlabel無しにするため
    {
        $label = $fav['title'];
    }

    if (empty($_conf['expack.iphone.toolbars.no_label']) && !empty($label) ) {
        $label = '<br>' . $label;
    } else {
        $label = '';
    }
    return _toolbar_i_button($icon, $label, $uri, $attrs);
}

// }}}
// {{{ toolbar_i_fav_button()

/**
 * お気にスレの登録・解除をトグルするツールバーボタン
 *
 * @param string $icon
 * @param string $label (fallback)
 * @param object $info @see lib/get_info.inc.php: get_thread_info()
 * @param int $setnum
 * @return string
 */
function toolbar_i_fav_button($icon, $label, $info, $setnum = 0)
{
    if (!array_key_exists($setnum, $info->favs)) {
        return toolbar_i_disabled_button($icon, $label);
    }

    $fav = $info->favs[$setnum];
    $attrs = ' onclick="return iutil.toolbarRunHttpCommand(this, event);"';
    if (!$fav['set']) {
        $attrs .= ' class="inactive"';
    }
    $uri = 'httpcmd.php?' . http_build_query(array(
        'cmd'       => 'setfav',
        'host'      => $info->host,
        'bbs'       => $info->bbs,
        'key'       => $info->key,
        'ttitle_en' => UrlSafeBase64::encode($info->ttitle),
        'setnum'    => $setnum,
        'setfav'    => 2,
    ), '', '&amp;');
    if(isset($label)) // nullだったらlabel無しにするため
    {
        $label = $fav['title'];
    }
    if (empty($_conf['expack.iphone.toolbars.no_label']) && !empty($label) ) {
        $label = '<br>' . $label;
    } else {
        $label = '';
    }

    return _toolbar_i_button($icon, $label, $uri, $attrs);
}

// }}}
// {{{ toolbar_i_palace_button()

/**
 * 殿堂入りの登録・解除をトグルするツールバーボタン
 *
 * @param string $icon
 * @param string $label
 * @param object $info @see lib/get_info.inc.php: get_thread_info()
 * @return string
 */
function toolbar_i_palace_menuItem($icon, $label, $info)
{
    $attrs = ' onclick="return iutil.toolbarRunHttpCommand(this, event);"';
    if (!$info->palace) {
        $attrs .= ' class="inactive"';
    }
    $uri = 'httpcmd.php?' . http_build_query(array(
        'cmd'       => 'setpal',
        'host'      => $info->host,
        'bbs'       => $info->bbs,
        'key'       => $info->key,
        'ttitle_en' => UrlSafeBase64::encode($info->ttitle),
        'setpal'    => 2,
    ), '', '&amp;');

    return toolbar_i_menuItem($icon, $label, $uri, $attrs);
}

// }}}
// {{{ toolbar_i_aborn_button()

/**
 * スレッドあぼーん状態をトグルするツールバーボタン
 *
 * @param string $icon
 * @param string $label
 * @param object $info @see lib/get_info.inc.php: get_thread_info()
 * @return string
 */
function toolbar_i_aborn_menuItem($icon, $label, $info)
{
    $attrs = ' onclick="return iutil.toolbarRunHttpCommand(this, event);"';
    if (!$info->taborn) {
        $attrs .= ' class="inactive"';
    }
    $uri = 'httpcmd.php?' . http_build_query(array(
        'cmd'       => 'taborn',
        'host'      => $info->host,
        'bbs'       => $info->bbs,
        'key'       => $info->key,
        'ttitle_en' => UrlSafeBase64::encode($info->ttitle),
        'taborn'    => 2,
    ), '', '&amp;');

    return toolbar_i_menuItem($icon, $label, $uri, $attrs);
}

// }}}
// {{{ toolbar_i_action_board_button()

/**
 * 外部アプリ等で板を開くボタン
 *
 * @param string $icon
 * @param string $label (fallback)
 * @param ThreadList $aThreadList
 * @return string
 */
function toolbar_i_action_board_button($icon, $label, ThreadList $aThreadList)
{
    global $_conf;

    $type = _toolbar_i_client_type();
    $pattern = $_conf["expack.tba.{$type}.board_uri"];
    if (!$pattern) {
        return toolbar_i_disabled_button($icon, $label);
    }

    $host = $aThreadList->host;
    $bbs = $aThreadList->bbs;
    $url = "http://{$host}/{$bbs}/";
    $uri = p2h(strtr($pattern, array(
        '$time' => time(),
        '$host' => rawurlencode($host),
        '$bbs'  => rawurlencode($bbs),
        '$url'  => $url,
        '$eurl' => rawurlencode($url),
    )), false);

    $title = $_conf["expack.tba.{$type}.board_title"];
    if ($title !== '') {
        $label = p2h($title, false);
    }
    if (empty($_conf['expack.iphone.toolbars.no_label']) && !empty($label) ) {
        $label = '<br>' . $label;
    } else {
        $label = '';
    }

    return _toolbar_i_button($icon, $label, $uri);
}

// }}}
// {{{ toolbar_i_action_thread_button()

/**
 * 外部アプリ等でスレッドを開くボタン
 *
 * @param string $icon
 * @param string $label (fallback)
 * @param Thread $aThread
 * @return string
 */
function toolbar_i_action_thread_button($icon, $label, Thread $aThread)
{
    global $_conf;

    $type = _toolbar_i_client_type();
    $pattern = $_conf["expack.tba.{$type}.thread_uri"];
    if (!$pattern) {
        return toolbar_i_disabled_button($icon, $label);
    }

    $url = $aThread->getMotoThread(true, '');
    $uri = p2h(strtr($pattern, array(
        '$time' => time(),
        '$host' => rawurlencode($aThread->host),
        '$bbs'  => rawurlencode($aThread->bbs),
        '$key'  => rawurlencode($aThread->key),
        '$ls'   => rawurlencode($aThread->ls),
        '$url'  => $url,
        '$eurl' => rawurlencode($url),
        '$path' => preg_replace('!^https?://!', '', $url),
    )), false);

    $title = $_conf["expack.tba.{$type}.thread_title"];
    if ($title !== '') {
        $label = p2h($title, false);
    }
    if (empty($_conf['expack.iphone.toolbars.no_label']) && !empty($label) ) {
        $label = '<br>' . $label;
    } else {
        $label = '';
    }

    return _toolbar_i_button($icon, $label, $uri);
}

// }}}
// {{{ toolbar_i_action_thread_button()

/**
 * 左上のバックボタン
 *
 * @param string $label
 * @return string
 */
function toolbar_i_back_button($label, $uri)
{
    global $_conf;


    return <<<EOS
<a href="{$uri}" id="backButton">{$label}</a>
EOS;
}

// {{{ _toolbar_i_client_type()

/**
 * クライアントの種類を返す
 *
 * @param void
 * @return string
 */
function _toolbar_i_client_type()
{
    global $_conf;

    switch ($_conf['client_type']) {
        case 'i':
            $type = UA::isAndroidWebKit() ? 'android' : 'iphone';
            break;
        case 'i':
            $type = 'mobile';
            break;
        case 'pc':
        default:
            $type = 'other';
    }

    return $type;
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
