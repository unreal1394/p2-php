<?php
set_include_path('phar://' . __DIR__ . '/p2pear.phar');
error_reporting(E_ALL & ~(E_NOTICE | E_STRICT | E_DEPRECATED));
require __DIR__ . '/vendor/.composer/autoload.php';
require __DIR__ . '/conf/conf.inc.php';
