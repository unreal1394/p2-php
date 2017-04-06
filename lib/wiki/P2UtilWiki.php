<?php

class P2UtilWiki
{
    /**
     * +Wiki:プロフィールIDからBEIDを計算する
     *
     * @return integer|0 成功したらBEIDを返す。失敗したら0を返す。
     */
    public static function calcBeId($prof_id)
    {
        for ($y = 2; $y <= 9; $y++) {
            for ($x = 2; $x <= 9; $x++) {
                $id = (($prof_id - $x*10.0 - $y)/100.0 + $x - $y - 5.0)/(3.0 * $x * $y);
                if ($id == floor($id)) {
                    return $id;
                }
            }
        }
        return 0;
    }

    /**
     * URLがイメピタならtrueを返す
     */
    public static function isUrlImepita($url)
    {
        return preg_match('{^http://imepita\.jp/}', $url);
    }
}
