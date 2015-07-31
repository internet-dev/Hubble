<?php
namespace Hubble\Api;

use Hubble\Library\Log;

/**
 * @describe:
 * @author: Jerry Yang(hy0kle@gmail.com)
 * */
class Test
{
    public static function ping($param)
    {
        Log::debug(__METHOD__);

        $data = array(
            'ping' => 'pong',
            'echo' => $param,
        );
        return $data;
    }

    public static function redis($param)
    {
        Log::debug(__METHOD__);

        $cache = new \Hubble\Library\Cache\Redis();
        $key = 'test';
        $val = 'abc';
        $set_ret = $cache->setKey($key)->setData($val)->set();
        $get_ret = $cache->setKey($key)->get();

        $data = array(
            'set_ret' => $set_ret,
            'get_ret' => $get_ret,
        );
        return $data;
    }

    public static function mongoDB($param)
    {
        Log::debug(__METHOD__);
    
        $mongo = new \Hubble\Library\Storage\Mongodb();
        $mongo->selectDb('test_db');
        // 获取表的记录
        $count = $mongo->count('test_table', array());
        Log::debug('count: ' . $count);
        // 插入记录
        $mongo->insert('test_table', array(
            'id' => 2,
            'title' => 'asdqw',
        ));
        //更新记录-存在时更新，不存在时添加-相当于set
        //$mongo->update('test_table', array('id'=>1),array('id'=>1,'title'=>'bbb'),array('upsert'=>1));
        //查找记录
        $f = $mongo->find('test_table', array('title' => 'asdqw'), array('start' => 2,'limit' => 2,'sort' => array('id' => 1)));
        Log::debug('f: ' . print_r($f, true));
        //查找一条记录
        $f_one = $mongo->findOne('test_table', array('id' => 2));
        Log::debug('f: ' . print_r($f, true));

        $data = array(
            'count' => $count,
            'f'     => $f,
            'f_one' => $f_one,
        );
        return $data;
    }
}
/* vi:set ts=4 sw=4 et fdm=marker: */

