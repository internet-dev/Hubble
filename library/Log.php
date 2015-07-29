<?php
namespace Hubble\Library;
/**
 * @describe:
 * @author: Jerry Yang(hy0kle@gmail.com)
 * */
class Log
{
    const LOG_DEBUG     = 1;
    const LOG_INFO      = 2;
    const LOG_NOTICE    = 3;
    const LOG_WARNING   = 4;
    const LOG_ERROR     = 5;
    const LOG_CRITICAL  = 6;
    const LOG_ALERT     = 7;
    const LOG_EMERGENCY = 8;

    private static function _log($level, $log)
    {
        $log_str = sprintf("[%s] [%s] %s\n", $level, date('Y-m-d H:i:s'), $log);
        echo $log_str;
    }

    public static function debug($log)
    {
        LOG_LEVEL <= self::LOG_DEBUG && self::_log('DEBUG', $log);
    }

    public static function info($log)
    {
        LOG_LEVEL <= self::LOG_INFO && self::_log('INFO', $log);
    }

    public static function notice($log)
    {
        LOG_LEVEL <= self::LOG_NOTICE && self::_log('NOTICE', $log);
    }

    public static function warning($log)
    {
        LOG_LEVEL <= self::LOG_WARNING && self::_log('WARNING', $log);
    }

    public static function error($LOG)
    {
        LOG_LEVEL <= self::LOG_ERROR && self::_log('ERROR', $log);
    }
}
/* vi:set ts=4 sw=4 et fdm=marker: */

