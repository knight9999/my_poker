<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta name="description" content="DrawPoker" />
<meta name="keywords" content="DrawPoker,php,yii" />
	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/standard.css" />
	
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body>
<div class="container" id="page">
	<div id="header">
		<div id="logo">
		<img src="<?php echo Yii::app()->baseUrl . "/images/poker_logo_s.png" ?>">
		</div>
	</div><!-- header -->

<?php 

$items = array(
				array('label'=>'Home', 'url'=>array('/poker/index')),
				array('label'=>'Play', 'url'=>array('/poker/menu')),
				array('label'=>'HowTo', 'url'=>array('/poker/howto')),
				array('label'=>'Settings', 'url'=>array('/poker/settings')),
				array('label'=>'Technical Note', 'url'=>array('/poker/techNote') )
);
if (Yii::app()->params['develop']) {
	array_push( $items , array('label' => 'Develop' , 'url'=>array('/pokerDevelop/index') ) );
}
	?>	
	
		<div id="mainmenu">
		<?php $this->widget('zii.widgets.CMenu',array(
			'id'=>"menu",
			'items'=>$items,
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