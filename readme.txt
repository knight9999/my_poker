Let following two directiories be writable.

protected/runtime
webroot/assets

Copy protected/config/basic.php.sample to protected/config/basic.php and 
edit it to set the YII_FRAMEWORK directory.

<?php 
define('YII_FRAMEWORK' , '/path/to/yii-framework');
?>


If you do not have yii framework, please download the latest version of Yii 1.1 from
http://www.yiiframework.com/
.

After expanding the tar.gz file, the YII_FRAMEWORK directory is for example,

yii-1.1.14.f0fee9/framework.


Then you set apache document root to the webroot dir. Then, you can play this game by 
access the server top page.

If you set this game under the sub directory of document root, for example

DOCUMENT_ROOT/my_poker/webroot

Then you access this game 

http://domain/my_poker/webroot.

This webroot dir (and my_poker dir) can be renamed.
However, when you change the relative path of webroot dir and protected dir ,
then the following lines in webroot/index.php file must be modified.

require_once(dirname(__FILE__).'/../protected/config/basic.php');
$yii=YII_FRAMEWORK . "/yii.php";
$config=dirname(__FILE__).'/../protected/config/main.php';



