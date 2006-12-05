<?php
/**
 * SJISのためのクラス。スタティックメソッドで利用する。
 * SJIS文字列の末尾が壊れているのを修正カットする。
 *
 * @author aki
 * @since  2006/10/02
 */
class StrSjis{

    /**
     * 参考データ
     * SJIS 2バイトの第1バイト範囲 129〜159、224〜239（0x81〜0x9F、0xE0〜0xEF）
     * SJIS 2バイトの第2バイト範囲 64〜126、128〜252（0x40〜0x7E、0x80〜0xFC）（第1バイト範囲を包括している）
     * SJIS 英数字(ASCII) 33〜126（0x21〜0x7E） 32 空白
     * SJIS 半角カナ161〜223（0xA1〜0xDF）(第2バイト領域) 
     */
    
    /*
    // SJIS文字化け文字が直前の第1バイト文字で打ち消されるかどうかの目視用テストコード
    // →打ち消されるが、末尾のみのチェックでは不足。先頭から順に2バイトの組を調べる必要がある。
    for ($i = 0; $i <= 255; $i++) {
        if (StrSjis::isSjis1stByte($i)) {
            for ($j = 0; $j <= 255; $j++) {
                if (StrSjis::isSjisCrasherCode($j)) {
                    echo $i . ' '. pack('C*', $i) . pack('C*', $j) . "<br><br>";
                }
            }
        }
    }
    */

    /**
     * SJIS文字列の末尾が、第一バイトであれば、タグが壊れる要因となるのでカットする。
     *
     * @access  public
     * @return  string
     */
    function fixSjis($str)
    {
        if (strlen($str) == 0) {
            return;
        }

        $un = unpack('C*', $str);
    
        $after_sjis1st = false;
        $after_crasher = false;
        foreach ($un as $v) {
            if ($after_sjis1st) {
                $after_sjis1st = false;
                $after_crasher = false;
            } else {
                if (StrSjis::isSjis1stByte($v)) {
                    $after_sjis1st = true;
                    $after_crasher = true;
                } elseif (StrSjis::isSjisCrasherCode($v)) {
                    $after_crasher = true;
                } else {
                    $after_crasher = false;
                }
            }
        }
    
        if ($after_crasher) {
            $str = substr($str, 0, -1);
        }
        return $str;
    
        /*
        // 末尾のみをチェックするためのコード。これでは不足。
        if (StrSjis::isSjisCrasherCode($un[$count]) && !StrSjis::isSjis1stByte($un[$count-1])) {
            $str = substr($str, 0, -1);
            return $str;
        }
        */
    }

    /**
     * SJISで末尾にあると（続く開始タグとくっついて）文字化けする可能性のあるコードの範囲（10進数）
     * 第1バイト範囲だけでなく第2バイト範囲でも文字化けするコードはある
     * 129-159 224-252 （目視で調べた）
     * 目視用テストコード
     * for ($i = 0; $i <= 255; $i++) {
     *    echo $i . ': '. pack('C*', $i) . "<br><br>";
     * }
     * （参考 SJIS 2バイトコード範囲のうちで1バイトコードに当てはまらないのは 128-160 224-252）
     *
     * @return  boolean  コード番号が文字化け範囲であれば true を返す
     */
    function isSjisCrasherCode($int)
    {
        if (129 <= $int && $int <= 159 or 224 <= $int && $int <= 252) {
            return true;
        }
        return false;
    }

    /**
     * SJIS 2バイトの第1バイト範囲かどうかを調べる 129〜159、224〜239（0x81〜0x9F、0xE0〜0xEF）
     *
     * @return  boolean  コード番号が第1バイト範囲であれば true を返す
     */
    function isSjis1stByte($int)
    {
        if (129 <= $int && $int <= 159 or 224 <= $int && $int <= 239) {
            return true;
        }
        return false;
    }

}
?>