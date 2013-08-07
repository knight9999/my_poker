<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta name="description" content="テストサイト" />
<meta name="keywords" content="テスト,サイト" />
<link rel="stylesheet" type="text/css" href="./index.css" />
	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/standard.css" />
	
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body>
<div class="container" id="page">
	<div id="header">
		<div id="logo">
		<img src="/images/poker_logo_s.png">
		</div>
	</div><!-- header -->

		<div id="mainmenu">
		<?php $this->widget('zii.widgets.CMenu',array(
			'id'=>"menu",
			'items'=>array(
				array('label'=>'Home', 'url'=>array('/site/index')),
				array('label'=>'About', 'url'=>array('/site/page', 'view'=>'about')),
				array('label'=>'Contact', 'url'=>array('/site/contact')),
				array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
			),
		)); ?>
	</div><!-- mainmenu -->
	<div class="clear"></div>
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>
	
	
	<?php echo $content; ?>
	

	<div class="clear"></div>

	<div id="footer">
	<div style="text-align: left;">
	<ul>
	  <li><a href="<?php echo $this->createUrl("poker/init") ?>">Poker</a></li>
	  <li><a href="<?php echo $this->createUrl("poker/menu") ?>">Poker/menu</a></li>
	  <li><a href="<?php echo $this->createUrl("poker/clearData") ?>">Poker/clearData</a></li>
	  </ul>
	</div>
		Copyright &copy; <?php echo date('Y'); ?> by My Company.<br/>
		All Rights Reserved.<br/>
		<?php echo Yii::powered(); ?>
	</div><!-- footer -->

	</div><!-- page -->
</body>

</html>