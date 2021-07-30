<?php
/**
 * Application configuration shared by all test types
 */
return [
	'id'            => 'yii2-swivel-package',
	'basePath'      => dirname(__DIR__),
	'language'      => 'en-US',
	'bootstrap'     => ['log'],
	'controllerMap' => [
		'migrate' => [
			'class'          => 'yii\console\controllers\MigrateController',
			'migrationPath'  => null,
			'migrationNamespaces'=>[
				'dhluther\\swivel\\migrations'
			]
		]
	],
	'aliases' => [
		'@bower'           => '@vendor/bower-asset',
		'@npm'             => '@vendor/npm-asset',
		'@dhluther/swivel' => dirname(__DIR__, 3).'/src', // '@vendor/dhluther/yii2-swivel/src'
	],
	'components'    => [
		'db'    => [
			'class'             => 'yii\db\Connection',
			'dsn'               => 'mysql:host=db;dbname=swivel',
			'username'          => 'root',
			'password'          => 'testtheswivels',
			'charset'           => 'utf8',
			'enableSchemaCache' => false,
			'emulatePrepare'    => true,
		],
		'cache' => [
			'class' => \yii\caching\DummyCache::class,
		],
		'log'   => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets'    => [
				'catchall' => [
					'class'      => 'yii\log\FileTarget',
					'logFile'    => '@app/runtime/logs/catch-all.log',
					'levels'     => ['info', 'warning', 'error'],
				],
			],
		]
	]
];
