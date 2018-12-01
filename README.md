## phalcon-framwork的调度器插件

#### 在服务中使用
``` php
$di->setShared('dispatcher', function () {
    $dispatcherConfig = $this->getConfig()->services->dispatcher;
    $dispatcher = new Dispatcher();
    if (isset($dispatcherConfig->module_default_namespaces)) {
        $dispatcher->setDefaultNamespace($dispatcherConfig->module_default_namespaces);
    }
    $eventsManager = new EventsManager();
    $dispatcherPlugin = new DIspatcherPlugin($dispatcherConfig->toArray());
    $eventsManager->attach('dispatch', $dispatcherPlugin);
    $dispatcher->setEventsManager($eventsManager);
    return $dispatcher;
});
```