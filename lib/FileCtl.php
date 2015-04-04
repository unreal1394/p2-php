<?php
// {{{ constants
/*
if (!defined('FILE_USE_INCLUDE_PATH')) {
    define('FILE_USE_INCLUDE_PATH', 1);
}

if (!defined('FILE_APPEND')) {
    define('FILE_APPEND', 8);
}
*/

if (in_array('compress.zlib', stream_get_wrappers())) {
    define('FILECTL_HAVE_COMPRESS_ZLIB_WRAPPER', 1);
} else {
    define('FILECTL_HAVE_COMPRESS_ZLIB_WRAPPER', 0);
}

// }}}
// {{{ FileCtl

/**
 * �t�@�C���𑀍삷��N���X
 * �C���X�^���X����炸�ɃN���X���\�b�h�ŗ��p����
 *
 * @static
 */
class FileCtl
{
    // {{{ make_datafile()

    /**
     * �������ݗp�̃t�@�C�����Ȃ���΍��
     *
     * @param string $file
     * @param int $perm
     * @return bool
     */
    static public function make_datafile($file, $perm = null)
    {
        // �f�t�H���g�̃p�[�~�b�V����
        $perm = intval($perm);
        if (($perm & 0777) === 0 || ($perm & ~0777) !== 0) {
            $default_perm = 0666 & umask();
            $perm = $default_perm ? $default_perm : 0666;
        }

        if (strpos($file, P2_NULLBYTE) !== false) {
            $epath = str_replace(P2_NULLBYTE, '\\0', $file);
            p2die("cannot make datafile. ({$epath})", '�t�@�C������NULL�o�C�g���܂܂�Ă��܂��B');
        }

        if (!file_exists($file)) {
            // �e�f�B���N�g����������΍��
            self::mkdirFor($file) or p2die("cannot make parent dirs. ({$file})");
            touch($file) or p2die("cannot touch. ({$file})");
            chmod($file, $perm);
        } else {
            if (!is_writable($file)) {
                $cont = self::file_read_contents($file);
                unlink($file);
                if (self::file_write_contents($file, $cont) === false) {
                    p2die('cannot write file.');
                }
                chmod($file, $perm);
            }
        }
        return true;
    }

    // }}}
    // {{{ mkdirFor()

    /**
     * �e�f�B���N�g�����Ȃ���΍��
     *
     * @param string $apath
     * @param int $perm
     * @return bool
     */
    static public function mkdirFor($apath, $perm = null)
    {
        return self::mkdirRecursive(dirname($apath), $perm);
    }

    // }}}
    // {{{ mkdirRecursive()

    /**
     * �f�B���N�g�����Ȃ���΍��
     *
     * @param string $apath
     * @param int $perm
     * @return bool
     */
    static public function mkdirRecursive($apath, $perm = null)
    {
        if (is_dir($apath)) {
            return true;
        } elseif (file_exists($apath)) {
            p2die("cannot mkdir, file already exists. ({$apath})");
        }

        // �f�t�H���g�̃p�[�~�b�V����
        $perm = intval($perm);
        if (($perm & 0777) === 0 || ($perm & ~0777) !== 0) {
            $default_perm = 0777 & umask();
            $perm = $default_perm ? $default_perm : 0777;
        }

        if (strlen($apath) === 0) {
            p2die("cannot mkdir. ({$apath})", '�f�B���N�g�������󔒂ł��B');
        }
        if (strpos($apath, P2_NULLBYTE) !== false) {
            $epath = str_replace(P2_NULLBYTE, '\\0', $apath);
            p2die("cannot mkdir. ({$epath})", '�f�B���N�g������NULL�o�C�g���܂܂�Ă��܂��B');
        }
        if (!@mkdir($apath, $perm, true)) {
            p2die("cannot mkdir. ({$apath})");
        }

        return true;
    }

    // }}}
    // {{{ mkdir_for()

    /**
     * mkdirFor() �̃G�C���A�X
     *
     * @param string $apath
     * @param int $perm
     * @return bool
     */
    static public function mkdir_for($apath, $perm = null)
    {
        return self::mkdirFor($apath, $perm);
    }

    // }}}
    // {{{ mkdir_r()

    /**
     * mkdirRecursive() �̃G�C���A�X
     *
     * @param string $apath
     * @param int $perm
     * @return bool
     */
    static public function mkdir_r($apath, $perm = null)
    {
        return self::mkdirRecursive($apath, $perm);
    }

    // }}}
    // {{{ get_gzfile_contents()

    /**
     * gz�t�@�C���̒��g���擾����
     */
    static public function get_gzfile_contents($filepath)
    {
        if (is_readable($filepath)) {
            if (FILECTL_HAVE_COMPRESS_ZLIB_WRAPPER) {
                return file_get_contents('compress.zlib://' . realpath($filepath));
            }
            ob_start();
            readgzfile($filepath);
            return ob_get_clean();
        } else {
            return false;
        }
    }

    // }}}
    // {{{ file_write_contents()

    /**
     * ��������t�@�C���ɏ�������
     * �ifile_put_contents()+����LOCK_EX�j
     *
     * @param string $filename
     * @param mixed $data
     * @param int $flags
     * @param resource $context
     */
    static public function file_write_contents($filename,
                                               $data,
                                               $flags = 0,
                                               $context = null
                                               )
    {
        return file_put_contents($filename, $data, $flags | LOCK_EX, $context);
    }

    // }}}
    // {{{ file_read_contents()

    /**
     * �t�@�C�����當�����ǂݍ���
     * �G���[�}���t���� @file_get_contents() �̑�p
     *
     * �}�N��PHP_STREAM_COPY_ALL�ɑΉ�����萔���Ȃ� (size_t��
     * ��ʓI�ɕ����Ȃ��APHP_INT_MAX���傫��) �̂ŁA-1�Ŕ��肷��
     *
     * @param string $filename
     * @param int $flags
     * @param resource $context
     * @param int $offset
     * @param int $maxlen
     */
    static public function file_read_contents($filename,
                                              $flags = 0,
                                              $context = null,
                                              $offset = -1,
                                              $maxlen = -1
                                              )
    {
        if (!is_readable($filename)) {
            return false;
        }
        if ($maxlen < 0) {
            if ($offset < 0) {
                return file_get_contents($filename, $flags, $context);
            }
            return file_get_contents($filename, $flags, $context, $offset);
        }
        return file_get_contents($filename, $flags, $context, $offset, $maxlen);
    }

    // }}}
    // {{{ gzfile_read_contents()

    /**
     * gzip���k���ꂽ�t�@�C�����當�����ǂݍ���
     * FileCtl::file_read_contents() �̑���
     *
     * @param string $filename
     * @param int $flags
     * @param resource $context
     * @param int $offset
     * @param int $maxlen
     */
    static public function gzfile_read_contents($filename,
                                                $flags = 0,
                                                $context = null,
                                                $offset = -1,
                                                $maxlen = -1
                                                )
    {
        if (!is_readable($filename)) {
            return false;
        }

        // {{{ compress.zlib �X�g���[�����b�p�[����

        if (FILECTL_HAVE_COMPRESS_ZLIB_WRAPPER) {
            $filename = 'compress.zlib://' . realpath($filename);
            if ($maxlen < 0) {
                if ($offset < 0) {
                    return file_get_contents($filename, $flags, $context);
                }
                return file_get_contents($filename, $flags, $context, $offset);
            }
            return file_get_contents($filename, $flags, $context, $offset, $maxlen);
        }

        // }}}
        // {{{ gzopen() ���g����

        if ($context !== null) {
            trigger_error('FileCtl::gzfile_read_contents(): context is not supported', E_USER_WARNING);
            return false;
        }

        $fp = gzopen($filename, 'rb', $flags & FILE_USE_INCLUDE_PATH);
        if (!$fp) {
            return false;
        }
        flock($fp, LOCK_SH);

        if ($offset > 0) {
            if (fseek($fp, $offset) == -1) {
                flock($fp, LOCK_UN);
                fclose($fp);
                return false;
            }
        }

        $content = '';

        if ($maxlen >= 0) {
            while (!feof($fp) && ($len = strlen($content)) < $maxlen) {
                if (($read = fread($fp, $maxlen - $len)) === false) {
                    $content = false;
                    break;
                }
                $content .= $read;
            }
        } else {
            while (!feof($fp)) {
                if (($read = fread($fp, 65536)) === false) {
                    $content = false;
                    break;
                }
                $content .= $read;
            }
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        return $content;

        // }}}
    }

    // }}}
    // {{{ file_read_lines()

    /**
     * �t�@�C���S�̂�ǂݍ���Ŕz��Ɋi�[����
     * �G���[�}���t���� @file() �̑�p
     *
     * @param string $filename
     * @param int $flags
     * @param resource $context
     */
    static public function file_read_lines($filename, $flags = 0, $context = null)
    {
        if (!is_readable($filename)) {
            return false;
        }
        $lines = file($filename, $flags, $context);
        if (($flags & FILE_IGNORE_NEW_LINES) && $lines &&
            strlen($lines[0]) && substr($lines[0], -1) == "\r")
        {
            $lines = array_map(create_function('$l', 'return rtrim($l, "\\r");'), $lines);
            if ($flags & FILE_SKIP_EMPTY_LINES) {
                $lines = array_filter($lines, 'strlen');
            }
        }
        return $lines;
    }

    // }}}
    // {{{ gzfile_read_lines()

    /**
     * gzip���k���ꂽ�t�@�C���S�̂�ǂݍ���Ŕz��Ɋi�[����
     * �G���[�}���t���� @gzfile() �̑�p
     *
     * $flags �Ƃ��� FILE_IGNORE_NEW_LINES, FILE_IGNORE_NEW_LINES,
     * FILE_SKIP_EMPTY_LINES ���T�|�[�g����̂� gzfile() ���֗��B
     *
     * @param string $filename
     * @param int $flags
     * @param resource $context
     */
    static public function gzfile_read_lines($filename, $flags = 0, $context = null)
    {
        if (!is_readable($filename)) {
            return false;
        }

        // {{{ compress.zlib �X�g���[�����b�p�[����

        if (FILECTL_HAVE_COMPRESS_ZLIB_WRAPPER) {
            return file('compress.zlib://' . realpath($filename), $flags, $context);
        }

        // }}}
        // {{{ gzopen() ���g����

        if ($context !== null) {
            trigger_error('FileCtl::gzfile_read_lines(): context is not supported', E_USER_WARNING);
            return false;
        }

        $lines = array();

        $ignore_new_lines = (($flags & FILE_IGNORE_NEW_LINES) != 0);
        $skip_empty_lines = (($flags & FILE_SKIP_EMPTY_LINES) != 0);

        $fp = gzopen($filename, 'rb', $flags & FILE_USE_INCLUDE_PATH);
        if (!$fp) {
            return false;
        }
        flock($fp, LOCK_SH);

        while (!feof($fp)) {
            $line = fgets($fp);
            if ($ignore_new_lines) {
                $line = rtrim($line, "\r\n");
            }
            if ($skip_empty_lines && strlen($line) == 0) {
                continue;
            }
            $lines[] = $line;
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        return $lines;

        // }}}
    }

    // }}}
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