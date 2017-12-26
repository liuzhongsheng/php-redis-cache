<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | Copyright (c) www.php63.cc All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 吹泡泡的鱼 <996674366@qq.com>
// +---------
class RedisCache
{
    protected $config;
    /**
     * @var string 缓存前缀
     */
    public $prefix = 'redis_';
    //用于存放对象信息
    private static $instance;

    //定义一个空的方法用于存放redis对象
    private $redis;

    //私有化构造函数防止被new
    private function __construct()
    {
        $this->redis = $this->connect();
    }

    //防止类被克隆
    private function __clone()
    {
        trigger_error('Clone is not allow!', E_USER_ERROR);
    }

    /**
     * 防止类重复实例化
     * 检测当前类是否已经实例化过，如果已经实例化过则实例化一次，否则返回之前的对象
     * @return RedisCache 返回实例化过后的对象
     */
    public static function getInstance()
    {
        //检测类是否实例化过
        if (!(self::$instance instanceof self)) {
            //如果没有实例化过则进行一次实例化
            self::$instance=new self();
        }
        return self::$instance;
    }

    /**
     * @return Redis 连接redis
     */
    private function connect()
    {
        try {
            //引入配置文件
            $this->config = include 'config.php';
            $redis = new \Redis();
            $redis->pconnect($this->config['host'], $this->config['port']);
            $redis->auth($this->config['password']);
            return $redis;
        } catch (RedisException $e) {
            echo 'phpRedis扩展没有安装：' . $e->getMessage();
            exit;
        }
    }

    /**
     * 为key设置过期时间
     * @param string 为key设置过期时间
     * @param int $time 过期时间（秒）
     * @return bool 成功返回 1 失败返回0
     */
    public function expire($key, $time = 3600)
    {
        return $this->redis->expire($this->prefix.$key, $time);
    }

    /**
     * 写入缓存
     * @param string $key 缓存key
     * @param string $value 缓存值
     * @return bool 成功返回 1 失败返回0
     */
    public function set($key = '', $value = '')
    {
        return $this->redis->set($this->prefix.$key, $value);
    }

    /**
     * 设置缓存
     * @param $key 缓存名称
     * @param string $value 缓存值
     * @param string $time 超时时间
     * @return bool 成功后返回1 失败后返回0
     */
    public function setCache($key, $value = '', $time='')
    {
        //设置缓存
        $result = $this->set($key, $value);
        //检测是否设置了过期时间，如果不为空则为该key设置过期时间
        if ($result == 1 && $time) {
            $result = $this->expire($key, $time);
        }
        return $result;
    }

    /**
     * 检查指定key是否存在
     * @param string $key
     * @return bool 若key存在返回1 不存在返回0
     */
    public function exists($key = '')
    {
        return $this->redis->exists($this->prefix.$key);
    }

    /**
     * 获取指定key
     * @param string $key 要查询的key
     * @return bool|string 存在返回获取到的值，不存在的时候返回null 如果不是字符串类型返回错误信息
     */
    public function getCache($key = '')
    {
        try{
            return $this->redis->get($this->prefix.$key);
        } catch (RedisException $e) {
            echo 'key只能是字符串：' . $e->getMessage();
            exit;
        }
    }

    /**
     * 删除指定的缓存
     * @param $key 要删除的key
     * @return int 成功返回受影响的行数
     */
    public function clearCache($key)
    {
        $data = $this->getCacheList($key);
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                 $this->redis->del($value);
            }
        }
        return true;
    }

    /**
     * 搜索所有key
     * @param $key 要搜索的key
     * @return int 成功返回受影响的行数
     */
    public function getCacheList($key = '*')
    {
        return $this->redis->keys($key);
    }
}