<?php
namespace Hubble\Common;

/**
 * @describe:
 * @author: Jerry Yang(hy0kle@gmail.com)
 * */
class ErrorCode
{
    const ERROR_UNKNOWN = -1;
    const SUCCESS       = 0;
    const CODE_DOES_NOT_EXIST = 1;
    const CONF_FILE_NOT_EXIST = 2;

    const LOST_REQUIRED_PARAMETERS          = 400101;
    const CAN_NOT_CONNECT_REDIS             = 400102;

    private static $message_conf = array(
        self::ERROR_UNKNOWN => '未知错误',
        self::SUCCESS       => 'OK',
        self::CODE_DOES_NOT_EXIST => '错误码不存在',
        self::CONF_FILE_NOT_EXIST => '配置文件不存在',

        self::LOST_REQUIRED_PARAMETERS      => '缺少必要参数',
        self::CAN_NOT_CONNECT_REDIS         => '无法连接 redis',
    );

    public static function getMessage($code)
    {
        $message = '';
        if (! isset(self::$message_conf[$code])) {
            $message = self::$message_conf[self::CODE_DOES_NOT_EXIST];
        } else {
            $message = self::$message_conf[$code];
        }

        return $message;
    }
}
/* vi:set ts=4 sw=4 et fdm=marker: */

