<?php
/* p2 - ユーザ定義用 設定ファイル
コメント冒頭の () 内はデフォルト値 */

//======================================================================
// ユーザ設定
//======================================================================

/* p2 認証設定 ====================================================== */
// 必ずこの認証をオンにするか、第三者にアクセスされないように自己対策を施すこと
$login['use'] = 1;	// (1) Basic認証を利用 (する:1, しない:0)

/* PATH =========================================================== */
// 取得スレッドのdat & idx データ保存ディレクトリ(パーミッションは707に) 
$datdir = "./data";	// ("./data")

// 初期設定データ保存ディレクトリ(パーミッションは707に) 
$prefdir = "./data";	// ("./data")

$first_page = "first_cont.php";	// ("first_cont.php") 右下部分に最初に表示されるページ。オンラインURLも可。

/*
板リストはオンラインとローカルの両方から読み込める
オンラインは $brdfile_online で設定
ローカルは ./board ディレクトリを作成し、その中にbrdファイルを置く（複数可）
*/

/* 板リストをオンラインURL($brdfile_online)から自動で読み込む。
指定先は menu.html 形式、2channel.brd 形式のどちらでもよい。
必要なければ、無指定("")にするか、コメントアウトしておく。 */
// ("http://azlucky.s25.xrea.com/2chboard/bbsmenu.html")	//2ch + 外部BBS
// ("http://www6.ocn.ne.jp/%7Emirv/2chmenu.html")	//2ch基本
$brdfile_online = "http://azlucky.s25.xrea.com/2chboard/bbsmenu.html";

/* subject ========================================================== */
$refresh_time = 20;	// (20) スレッド一覧の自動更新間隔。（分指定。0なら自動更新しない。）
$_conf['sb_show_spd'] = 0;	// (0) スレッド一覧ですばやさを表示 (する:1, しない:0)
$_conf['sb_show_fav'] = 0;	// (0) スレッド一覧でお気にスレマーク★を表示 (する:1, しない:0)
$sort_zero_adjust = 0.1;	// (0.1) 新着ソートでの「既得なし」の「新着数ゼロ」に対するソート優先順位 (上位:0.1, 混在:0, 下位:-0.1)
$cmp_dayres_midoku = 1;	// (1) 勢いソート時に新着レスのあるスレを優先 (する:1, しない:0)
$k_sb_disp_range = 30;	// (30) 携帯閲覧時、一度に表示するスレの数
$c_viewall_kitoku = 1;	// (1) 既得スレは表示件数に関わらず表示 (する:1, しない:0)

/* read ============================================================ */
$respointer = 1;	// (1) スレ閲覧時、未読の何コ前のレスにポインタを合わせるか
$before_respointer = 20;	// (20) ポインタの何コ前のレスから表示するか
$before_respointer_new = 0;	// (0) 新着まとめ読みの時、ポインタの何コ前のレスから表示するか
$preview_thumbnail = 0;	// (0) 画像URLの先読みサムネイル (表示する:1, しない:0)
$pre_thumb_height = "32";	// ("32") 画像サムネイルの縦の大きさを指定（ピクセル）
$pre_thumb_width = "32";	// ("32") 画像サムネイルの横の大きさを指定（ピクセル）
$brocra_checker['use'] = 0;	// (0) ブラクラチェッカ (つける:1, つけない:0)
$iframe_popup = 2;	// (2) HTMLポップアップ（する:1, しない:0, pでする:2）
$iframe_popup_delay = 0.2;	// (0.2) HTMLポップアップの表示遅延時間（秒）
$ext_win_target = "";	// ("") 外部サイト等へジャンプする時に開くウィンドウのターゲット名（同窓:"", 新窓:"_blank"）
$bbs_win_target = "";	// ("") p2対応BBSサイト内でジャンプする時に開くウィンドウのターゲット名（同窓:"", 新窓:"_blank"）
$bottom_res_form = 1;	// (1) スレッド下部に書き込みフォームを表示（する:1, しない:0）
$quote_res_view = 1;	// (1) 引用レスを表示（する:1, しない:0）
$k_rnum_range = 10;	// (10) 携帯閲覧時、一度に表示するレスの数

/* ETC ============================================================= */
$_conf['be_2ch_code'] = "";	// ("") be.2ch.netの認証コード(パスワードではない)
$_conf['be_2ch_mail'] = "";	// ("") be.2ch.netの登録メールアドレス
$get_new_res = 200;	// (200) 新しいスレッドを取得した時に表示するレス数(全て表示する場合:"all")
$rct_rec_num = 20;	// (20) 最近読んだスレの記録数
$res_hist_rec_num = 20;	// (20) 書き込み履歴の記録数
$res_write_rec = 1;	// (1) 書き込み内容を記録(する:1, しない:0)
$updatan_haahaa = 1;	// (1) p2の最新バージョンを自動チェック(する:1, しない:0)
$_conf['through_ime'] = "p2pm";	// ("p2pm") 外部URLジャンプする際に通すゲート。（直接:"", p2 ime(自動転送):"p2", p2 ime(手動転送):"p2m", p2 ime(pのみ手動転送):"p2pm"）
$join_favrank = 1;	// (1) お気にスレ共有に参加（する:1, しない:0）
$c_enable_menu_new = 0;	// (0) 板メニューに新着数を表示（する:1, しない:0, お気に板のみ:2）
$c_menu_refresh_time = 0;	// (0) 板メニュー部分の自動更新間隔（分指定。0なら自動更新しない。）

$proxy['use'] = 0;	// (0) プロキシを利用(する:1, しない:0)
$proxy['host'] = "";	// ("") プロキシホスト ex)"127.0.0.1", "www.p2proxy.com"
$proxy['port'] = "";	// ("") プロキシポート ex)"8080"

?>
