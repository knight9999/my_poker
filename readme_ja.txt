インストールの仕方

まず、以下のディレクトリを書き込み可能にしてください。

protected/runtime
webroot/assets

また、protected/config/basic.php.sampleファイルをprotected/config/basic.phpファイルにリネームして
、さらにそのファイルを編集して、YII_FRAMEWORKディレクトリをセットしてください。

<?php 
define('YII_FRAMEWORK' , '/path/to/yii-framework');
?>

もし、まだYII_FRAMEWORKがない場合、Yii 1.1の最新版をhttp://www.yiiframework.com/からダウンロードして
ください。

tar.gzファイルを展開したら、YII_FRAMEWORKディレクトリは縦叔母

yii-1.1.14.f0fee9/framework

になります。

次に、Apacheのドキュメントルートがwebrootディレクトリになるように設定してください。こうすれば、サーバのトップページに
アクセスうることで、ゲームを始めることが出来ます。

そうではなく、このゲームをドキュメントルートのサブディレクトリに配置した場合、たとえば

DOCUMENT_ROOT/my_poker/webroot

とした場合は、次のようにアクセスしてください。

http://domain/my_poker/webroot

このwebrootディレクトリ（およびmy_pokerディレクトリ)はリネームすることも出来ます。
ただし、webrootディレクトリとprotectedディレクトリの相対パスが変わってしまう場合は、
webroot/index.phpファイルの次の行を修正してください。

require_once(dirname(__FILE__).'/../protected/config/basic.php');
$yii=YII_FRAMEWORK . "/yii.php";
$config=dirname(__FILE__).'/../protected/config/main.php';

