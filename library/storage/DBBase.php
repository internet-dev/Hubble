<?php
namespace Hubble\Library\Storage;

use Hubble\Library\Log;

/**
 * @describe:
 * @author: Jerry Yang(hy0kle@gmail.com)
 * */
abstract class DBBase
{
    private static $_mode = '';
    // 单例容器
    private static $_db_container = array();

    protected static $_db = null;

    abstract public function connect();

    protected static function _createConnect($mode)
    {
        self::$_mode = $mode;

        $conf = \Hubble\Config\Config::get('mysql');
        $conf = $conf[$mode];
        $dsn  = "mysql:dbname={$conf['db']};host={$conf['host']};port={$conf['port']}";
        $db = new \PDO($dsn, $conf['user'], $conf['password']);
        // 设置为异常模式
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        // 忽略掉配置的字符,强制使用 utf8
        $db->query('SET NAMES UTF8');

        return $db;
    }

    public static function getInstance()
    {
        $class = get_called_class();
        // instanceof
        if (isset(self::$_db_container[$class])) {
            return self::$_db_container[$class];
        } else {
            $instance = new $class();
            self::$_db = $instance->connect();

            self::$_db_container[$class] = $instance;
            return $instance;
        }
    }

    public function getDB()
    {
        return self::$_db;
    }

    /**
     * @brief query 屏蔽掉 $db 句柄, 执行 sql 语句的标准入口
     *
     * @param: $sql
     *
     * @return:
     */
    public static function query($sql)
    {
        $db = self::getInstance()->getDb();
        Log::debug(sprintf('[%s] %s', self::$_mode, $sql));

        return $db->query($sql, \PDO::FETCH_ASSOC);
    }

    /*
     * @brief select 以数组方式返回 select 的结果集
     *
     * @param: $sql
     *
     * @return: array
     */
    public static function select($sql)
    {
        return self::query($sql);
    }

    /**
     * get一行数据
     *
     * @param string $sql
     * @return array
     */
    public static function getOne($sql)
    {
        $res = self::select($sql);
        return $res->fetch();
    }

    /**
     * get多行数据
     *
     * @param string $sql
     * @return array
     */
    public static function getAll($sql)
    {
        $res = self::select($sql);
        return $res->fetchAll();
    }

    /**
     * 通用insert方法
     * @param array $save_data
     * @param string $table
     * @return array $unEscape
     */
    public static function insert($save_data, $table, $unEscape = array())
    {
        $set = array();
        foreach ($save_data as $field => $value)
        {
            if (!in_array($field, $unEscape)) {
                $value = \Hubble\Library\Util::escape($value);
            }
            $set[] = "`{$field}` = '{$value}'";
        }
        $sql = sprintf('INSERT INTO `%s` SET %s', $table, implode(', ', $set));
        self::query($sql);

        return self::lastInsertId();
    }

    /**
     * @brief lastInsertId 取最后插入的 id
     *
     * @return: int | bool
     */
    public static function lastInsertId()
    {
        $db = self::getInstance()->getDb();
        return $db->lastInsertId();
    }

    /**
     * 通用update方法
     * @param array  $save_data
     * @param string $table
     * @param array  $unEscape
     * @return bool
     */
    public static function update($save_data, $table, $id, $unEscape = array())
    {
        $set = array();
        foreach ($save_data as $field => $value)
        {
            if (!in_array($field, $unEscape)) {
                $value = \Hubble\Library\Util::escape($value);
            }
            $set[] = "`{$field}` = '{$value}'";
        }
        $sql = sprintf('UPDATE `%s` SET %s WHERE id = %d', $table, implode(', ', $set), $id);
        return self::query($sql);
    }
}
/* vi:set ts=4 sw=4 et fdm=marker: */

