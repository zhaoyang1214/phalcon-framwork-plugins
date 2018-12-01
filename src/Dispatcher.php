<?php
/**
 * @desc 调度器插件
 */
namespace PhalconPlugins;

use Exception;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Dispatcher as PhalconDispatcher;
use Phalcon\Mvc\Dispatcher\Exception as DispatcherException;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;

class Dispatcher extends Plugin
{

    private $servicesConfig = [];

    public function __construct(array $servicesConfig)
    {
        $this->servicesConfig = $servicesConfig;
    }

    /**
     * 处理 Not-Found 错误
     */
    public function beforeException(Event $event, MvcDispatcher $dispatcher, Exception $exception)
    {
        // 可直接访问config服务
        // $notfoundConfig = $this->config->services->dispatcher->notfound;
        $notfoundConfig = $this->servicesConfig['notfound'];
        // 处理404异常
        if ($exception instanceof DispatcherException) {
            $dispatcher->forward([
                'namespace' => $notfoundConfig['namespace'],
                'controller' => $notfoundConfig['controller'],
                'action' => $notfoundConfig['action']
            ]);
            return false;
        }
        
        // 代替控制器或者动作不存在时的路径
        switch ($exception->getCode()) {
            case PhalconDispatcher::EXCEPTION_HANDLER_NOT_FOUND:
            case PhalconDispatcher::EXCEPTION_ACTION_NOT_FOUND:
                $dispatcher->forward([
                    'namespace' => $notfoundConfig['namespace'],
                    'controller' => $notfoundConfig['controller'],
                    'action' => $notfoundConfig['action']
                ]);
                return false;
        }
    }

    /**
     * 组合pathinfo参数
     */
    public function beforeDispatchLoop(Event $event, MvcDispatcher $dispatcher)
    {
        $params = $dispatcher->getParams();
        $newParams = [];
        foreach ($params as $k => $v) {
            if ($k & 1) {
                $newParams[$params[$k - 1]] = $v;
            }
        }
        $dispatcher->setParams($newParams);
    }
}
