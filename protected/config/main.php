<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

function _joinpath($dir1, $dir2) {
	return realpath($dir1 . '/' . $dir2);
}

$homePath      = dirname(__FILE__) . '/../..';
$protectedPath = _joinpath($homePath, 'protected');
$webrootPath   = _joinpath($homePath, 'webroot');
$runtimePath   = _joinpath($homePath, 'runtime');

return array(
	'basePath'=>$protectedPath,
	'runtimePath' => $runtimePath,
	'name'=>'My Poker',
	'defaultController'=>'poker',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.models.operations.*',
		'application.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'guest',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('*'),
		),
	),

	// application components
	'components'=>array(
		'settings' => array(
			'class'=>'application.components.Settings'
		),
//		'user'=>array(
//			// enable cookie-based authentication
//			'allowAutoLogin'=>true,
//		),
		// uncomment the following to enable URLs in path-format
		/*
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		*/
//		'db'=>array(
//			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
//		),
		// uncomment the following to use a MySQL database
		/*
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=testdrive',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		),
		*/
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'poker/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'fine14r3@gmail.com',
	),

	'sourceLanguage'=>'00',
	'language'=>'jp',
		
);