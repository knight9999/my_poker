<?php

// change the following paths if necessary
// $yiit='/var/yii/framework/yiit.php';
require_once(dirname(__FILE__).'/../config/basic.php');
$yiit=YII_FRAMEWORK . "/yiit.php";
$config=dirname(__FILE__).'/../config/test.php';

require_once($yiit);
require_once(dirname(__FILE__).'/WebTestCase.php');

Yii::createWebApplication($config);
