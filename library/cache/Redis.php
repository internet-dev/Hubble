<?php

namespace Hubble\Library\Cache;

/**
 * Redis 处理类
 */
class Redis
{

    private $exception;
    private $prefix = null;
    private $key = null;
    private $data = array ();
    private $timeout = 0;
    private $expire = 0;
    private $length = 0;
    private $field = null;

    public function __construct()
    {
    }

    public function setPrefix($prefix)
    {
        if ($prefix) {
            $this->prefix = $prefix;
        }

        return $this;
    }

    public function setKey($key)
    {
        if ($key) {
            $this->key = $key;
        }
        return $this;
    }

    public function setField($field)
    {
        if ($field) {
            $this->field = $field;
        }
        return $this;
    }

    public function setExpire($expire)
    {
        if ($expire) {
            $this->expire = $expire;
        }
        return $this;
    }

    public function setLength($length)
    {
        if ($length) {
            $this->length = $length;
        }
        return $this;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * 返回组合后的key
     * @return string OR array
     */
    protected function getKey()
    {
        $key = $this->key;
        if (!is_array($key)) {
            $data = ($this->prefix == null ? '' : ':') . $key;
        } else {
            $data = array ();
            foreach ($key as $row) {
                $data[$row] = ($this->prefix == null ? '' : ':') . $key;
            }
        }
        return $data;
    }

    public function get()
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }
        try {
            if (!is_array($key)) {
                $result = $this->getMasterConnection()->get($key);
            } else {
                $result = $this->getMasterConnection()->mget($key);
            }
        } catch (\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
        return $result;
    }

    public function set()
    {
        $key = $this->getKey();
        $expire = $this->expire;
        if (empty($key)) {
            return false;
        }

        try {
            if (!is_array($key)) {
                if($expire!=0){
                    $result = $this->getMasterConnection()->set($key, $this->data, $expire);
                } else{
                    $result = $this->getMasterConnection()->set($key, $this->data);
                }

            } else {
                $data = array ();
                foreach ($key as $k => $v) {
                    if (!isset($this->data[$k])) {
                        continue;
                    }
                    $data[$v] = $this->data[$k];
                }
                $result = $this->getMasterConnection()->mset($data);
            }
        } catch (\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
        return $result;
    }

    public function exists()
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }

        try {
            if (is_array($key)) {
                $result = array ();
                foreach ($key as $k => $v) {
                    $result[$k] = $this->getSlaveConnection()->exists($v);
                }
            } else {
                $result = $this->getSlaveConnection()->exists($key);
            }
        } catch (\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
        return $result;
    }

    public function lrange($start = 0, $end = -1)
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }

        try {
            return $this->getSlaveConnection()->lrange($key, $start, $end);
        } catch (\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
    }

    public function lPush()
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }

        try {
            return $this->getMasterConnection()->lPush($key, $this->data);
        } catch (\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
    }

    public function lTrim($start = 0, $end = -1)
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }

        try {
            return $this->getMasterConnection()->ltrim($key, $start, $end);
        } catch (\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
    }


    public function rPush()
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }

        try {
            return $this->getMasterConnection()->rPush($key, $this->data);
        } catch (\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
    }

    public function lPop()
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }

        try {
            return $this->getMasterConnection()->lPop($key);
        } catch (\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
    }

    public function rPop()
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }

        try {
            return $this->getMasterConnection()->rPop($key);
        } catch (\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
    }

    public function lLen()
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }

        try {
            return $this->getSlaveConnection()->lLen($key);
        } catch (\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
    }

    public function incr($value = 1)
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }

        try {
            return $this->getMasterConnection()->incr($key, $value);
        } catch (\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
    }

    public function decr($value = 1)
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }

        try {
            return $this->getMasterConnection()->decr($key, intval($value));
        } catch (\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
    }

    public function delete()
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }

        try {
            return $this->getMasterConnection()->delete($key);
        } catch (\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
    }

    public function hdel()
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }
        $field = $this->field;
        if (empty($field)) {
            return false;
        }

        try {
            return $this->getMasterConnection()->hDel($key, $field);
        } catch (\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
    }

    public function hset()
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }
        $field = $this->field;
        if (empty($field)) {
            return false;
        }

        $data = $this->data;
        try {
            return $this->getMasterConnection()->hSet($key, $field, $data);
        } catch (\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
    }

    public function hget()
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }
        $field = $this->field;
        if (empty($field)) {
            return false;
        }

        try {
            return $this->getMasterConnection()->hGet($key, $field);
        } catch(\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
    }

    public function hincr()
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }
        $field = $this->field;
        if (empty($field)) {
            return false;
        }

        try {
            return $this->getMasterConnection()->hIncrBy($key, $field, 1);
        } catch (\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
    }

    public function hdecr()
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }
        $field = $this->field;
        if (empty($field)) {
            return false;
        }

        try {
            return $this->getMasterConnection()->hIncrBy($key, $field, -1);
        } catch (\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
    }

    public function zadd()
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }
        $field = $this->field;
        if (empty($field)) {
            return false;
        }

        $data = $this->data;
        try {
            return $this->getMasterConnection()->zAdd($key, $field, $data);
        } catch (\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
    }

    public function zrevrang()
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }
        $length = $this->length;
        if (empty($length)) {
            return false;
        }

        try {
            return $this->getMasterConnection()->zrevrange($key, 0, $length);
        } catch(\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
    }

    public function zscore()
    {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }
        $field = $this->field;
        if (empty($field)) {
            return false;
        }

        try {
            return $this->getMasterConnection()->zScore($key, $field);
        } catch(\Exception $ex) {
            $this->exception = $ex;
            return false;
        }
    }

    public function getLastException()
    {
        return $this->exception;
    }

    public function getMasterConnection()
    {
        return $this->getConnection(false);
    }

    public function getSlaveConnection()
    {
        return $this->getConnection();
    }

    protected function getConnection($isSlave = true)
    {
        static $pool = array ();
        $cacheName = get_called_class();
        if ( !in_array($cacheName, array_keys($pool)) ) {
            $cfg = \Hubble\Config\Config::get('redis');
            if ($isSlave) {
                $cfg = $cfg['slaves'];
            } else {
                $cfg = $cfg['master'];
            }
            $pool[$cacheName] = $this->connect($cfg);
        }
        return $pool[$cacheName];
    }

    protected function connect($cfg)
    {
        try {
            $redis = new \Redis();
            $redis->connect($cfg['host'], $cfg['port'], $this->timeout);
        } catch (\Exception $ex) {
            $message = "Redis connection failed : [" . $cfg['host'] . ';' . $cfg['port'] . ']';
            \Hubble\Library\Log::error($message . 'common.class.redis.connect');

            throw new \Hubble\Common\HubbleException(\Hubble\Common\ErrorCode::CAN_NOT_CONNECT_REDIS);
        }

        return $redis;
    }

    /**
     *
     * @param type $num
     * @param flag 1主库 0从库
     */
    public function selectDb($num = 0, $isMaster = true)
    {
        if ($isMaster) {
            $this->getMasterConnection()->select($num);
        } else{
            $this->getSlaveConnection()->select($num);
        }

        return $this;
    }

    public function expire($time = 0)
    {
        $rtn = $this->getMasterConnection()->expire($this->key, $time);
        return $rtn;
    }
}

