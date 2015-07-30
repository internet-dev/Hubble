<?php
namespace Hubble;
/**
 * @describe:
 * @author: Jerry Yang(hy0kle@gmail.com)
 * */
/** 基本常量 */
define('WORK_PATH',     __DIR__);
define('CONFIG_PATH',   WORK_PATH . '/config');

// 解析命令行参数
$color_off    = "\e[0m";
$color_red    = "\e[1;31m";
$color_green  = "\e[32m";
$color_yellow = "\e[33m";
$color_purple = "\e[35m";
$color_cyan   = "\e[1;36m";
if (! ($argc > 1) || ('develop' != $argv[1] && 'product' != $argv[1])) {
    echo $color_red, 'Lost OR wrong cli arguments.' , $color_off, PHP_EOL;
    echo '```', PHP_EOL;
    echo $color_cyan, 'Usage: ', $color_green, 'php ' , $argv[0],
        $color_yellow, ' RUNTIME_ENVIROMENT(product|develop) log_level[(0-8)]', $color_off, PHP_EOL;
    echo '```', PHP_EOL;
    exit;
}
define('RUNTIME_ENVIROMENT', $argv[1]);

$log_level = 0;
if ($argc >= 3 && $argv[2] >= 0) {
    $log_level = $argv[2] + 0;
}
define('LOG_LEVEL', $log_level);

// 自动加载器
include_once(WORK_PATH . '/library/Autoload.php');

// 启动 mysql proxy

// 启动主服务
$srv = new App\Server(array());
$srv->run();

//\SeasLog::info(sprintf('启动完成: %s', date('Y-m-d H:i:s')));
/* vi:set ts=4 sw=4 et fdm=marker: */

