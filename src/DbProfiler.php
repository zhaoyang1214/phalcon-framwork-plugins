<?php
/** 
 * @desc SQL语句性能分析插件 
 */
namespace PhalconPlugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;

class DbProfiler extends Plugin
{

    /**
     * 执行sql语句前执行
     *
     * @param Event $event
     *            事件
     * @param $connection 数据库连接            
     */
    public function beforeQuery(Event $event, $connection)
    {
        $this->profiler->startProfile($connection->getSQLStatement(), $connection->getSqlVariables(), $connection->getSQLBindTypes());
    }

    /**
     * 执行sql语句前执行
     *
     * @param Event $event
     *            事件
     * @param $connection 数据库连接            
     */
    public function afterQuery(Event $event, $connection)
    {
        $profiler = $this->profiler;
        $profiler->stopProfile();
        $profile = $profiler->getLastProfile();
        $sql = $profile->getSQLStatement();
        $params = $profile->getSqlVariables();
        $params = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $executeTime = $profile->getTotalElapsedSeconds();
        $profiler->reset();
        $this->di->getLogger()->info("$sql $params $executeTime");
    }
}
