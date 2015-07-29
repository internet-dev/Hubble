<?php
namespace Hubble\Config;

/**
 * @describe:
 * @author: Jerry Yang(hy0kle@gmail.com)
 * */
final class Config
{
    private static $_config_map = array();

    private function __construct()
    {
        // 不能被实例化
    }

    public static function get($conf)
    {
        if (isset(self::$_config_map[$conf])) {
            return self::$_config_map[$conf];
        } else {
            // 解析配置文件,并放入静态容器
            $file_name = sprintf('%s/%s/%s.ini', CONFIG_PATH, RUNTIME_ENVIROMENT, $conf);
            if (! file_exists($file_name)) {
                throw new \Hubble\Common\HubbleException(\Hubble\Common\ErrorCode::CONF_FILE_NOT_EXIST);
            }
            // Parse without sections
            $parse_config = parse_ini_file($file_name);
            self::$_config_map[$conf] = $parse_config;

            return $parse_config;
        }
    }
}
/* vi:set ts=4 sw=4 et fdm=marker: */

