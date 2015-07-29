<?php
namespace Hubble\App;

use Hubble\Library\Log;
/**
 * @describe: 服务入口
 * @author: Jerry Yang(hy0kle@gmail.com)
 * @note: SeasLog 在长驻服务模式下内存泄漏的问题,改为 echo.
 * */
class Server
{
    public static  $swoole = null;
    private static $_server = null;
    // 服务端默认参数
    private static $srv_conf = array(
        'worker_num' => 8,      // 工作进程数量,开发环境可以调小
        'daemonize'  => false,  // 是否作为守护进程,开发环境默认为 false,方便调试
        'max_request'=> 1024,

        /** worker进程数据包分配模式 */
        'dispatch_mode' => 2,   // 1: 平均分配，2: 按FD取摸固定分配，3: 抢占式分配，默认为取摸(dispatch = 2)

        /** 心跳检测机制 */
        'heartbeat_check_interval'  => 30,
        'heartbeat_idle_time'       => 60,

        /** 日志 */
        'log_file' => '/home/work/webdata/logs/swoole.log',
    );

    public function __construct($run_conf)
    {
        if (null !== self::$_server) {
            return self::$_server;
        } else {
            self::$srv_conf = array_merge(self::$srv_conf, $run_conf);

            self::$_server = $this;
            //var_dump(self::$srv_conf);
            return self::$_server;
        }
    }

    public function _connect($server, $fd)
    {
        $log = sprintf('[fd: %d] has connect.', $fd);
        Log::info($log);
    }

    public function _receive($server, $fd, $from_id, $data)
    {
        $log = sprintf('[fd: %d] [from_id: %d] [data: %s]', $fd, $from_id, $data);
        Log::debug($log);

        $response = array(
            'code'    => \Hubble\Common\ErrorCode::ERROR_UNKNOWN,
            'message' => 'unknown error',
            'data'    => new \ArrayObject(),
        );

        try {
            $json_req = json_decode($data, true);
            if (! is_array($json_req) || ! isset($json_req['cmd'])
                || ! isset($json_req['parameters']) || ! is_array($json_req['parameters'])) {
                $response['code']    = \Hubble\Common\ErrorCode::LOST_REQUIRED_PARAMETERS;
                $response['message'] = \Hubble\Common\ErrorCode::getMessage(\Hubble\Common\ErrorCode::LOST_REQUIRED_PARAMETERS);

                $log = '错误的请求. ' . $log;
                Log::warning($log);
            } else {
                $response['code']    = \Hubble\Common\ErrorCode::SUCCESS;
                $response['message'] = 'OK';
                $cmd = $json_req['cmd'];
                unset($json_req['cmd']);
                $response['data'] = call_user_func_array($cmd, $json_req);
            }
        } catch (\Exception $e) {
            $response['code']    = $e->getCode();
            $response['message'] = $e->getMessage();
        }

        $server->send($fd, json_encode($response));
        $server->close($fd);
    }

    public function _close($server, $fd)
    {
        $log = sprintf('[fd: %d] has closed', $fd);
        Log::info($log);
    }

    public function run()
    {
        $conf = \Hubble\Config\Config::get('server');
        //print_r($conf);exit;
        $swoole = new \swoole_server($conf['host'], $conf['port']);
        self::$swoole = $swoole;

        $swoole->set(self::$srv_conf);

        $swoole->on('connect', array($this, '_connect'));
        $swoole->on('receive', array($this, '_receive'));
        $swoole->on('close',   array($this, '_close'));

        Log::info('开始工作');

        $swoole->start();
    }
}
/* vi:set ts=4 sw=4 et fdm=marker: */

