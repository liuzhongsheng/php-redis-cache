# php-redis-cache php+redis缓存类
<h3>使用说明：</h3>
<pre>
//引入类
$result = require APP_PATH.'/plugin/RedisCache/RedisCache.class.php';
$obj = \RedisCache::getInstance();
<br>
//写入持久化的数据
$obj ->setCache('testCache','这是测试缓存没有过期时间');
<br>
//写入60秒过期的数据
$obj ->setCache('testCacheOutTime','这是测试缓存过期时间60秒', 60);
<br>
//删除数据
$obj->delCache('testCacheOutTime');
<br>
//检测数据是否存在
$obj->exists('key'));
<br>
//获取数据
$obj->getCache('key');
</pre>

<h3>配置文件说明：</h3>
<pre>
return [
    'start_using'   =>  'off',  //on 开 off关闭
    'host'          =>  '127.0.0.1',    //服务地址
    'port'          =>  6379,   //服务端口号
    'password'      => '',//redis密码
]
</pre>