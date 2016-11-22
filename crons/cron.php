<?php

//exec("/usr/local/php55/bin/php /home/byustudi/byustudio.in.ua/ztv/yii cron-task/sync");

require(dirname(__DIR__) . '/vendor/autoload.php');
require(dirname(__DIR__) . '/vendor/yiisoft/yii2/Yii.php');

$config = require(dirname(__DIR__) . '/config/console.php');

$application = new yii\console\Application($config);
$application->runAction('cron-task/sync');
?>