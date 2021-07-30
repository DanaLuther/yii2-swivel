<?php
/**
 * SwivelLoggerTest.php
 *
 * @author Dana Luther <dana.luther@gmail.com>
 */

namespace dhluther\swivel\tests\unit;

use dhluther\swivel\SwivelLogger;
use Psr\Log\LogLevel;
use yii\log\Logger;

class SwivelLoggerTest extends \Codeception\Test\Unit
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	public function testCreateSwivelLogger()
	{
		$logger = \Yii::createObject(SwivelLogger::class);
		$this->assertInstanceOf(SwivelLogger::class, $logger);
	}

	/**
	 * @depends testCreateSwivelLogger
	 */
	public function testCategory()
	{
		$logger = \Yii::createObject(SwivelLogger::class);
		$logger->setLogCategory('test.application');
		$this->assertEquals('test.application', $logger->getLogCategory());
	}

	public function getLogLevelList()
	{
		return [
			'asInt1'  => [1, Logger::LEVEL_ERROR],
			'asInt4'  => [4, Logger::LEVEL_INFO],
			'debug'   => [LogLevel::DEBUG, Logger::LEVEL_TRACE],
			'error'   => [LogLevel::ERROR, Logger::LEVEL_ERROR],
			'warning' => [LogLevel::ALERT, Logger::LEVEL_WARNING],
			'info'    => [LogLevel::INFO, Logger::LEVEL_INFO],
			'notice'  => [LogLevel::NOTICE, Logger::LEVEL_INFO],
		];
	}

	/**
	 * @dataProvider getLogLevelList
	 */
	public function testLevels($level,$expected)
	{
		$logger = \Yii::createObject(SwivelLogger::class);
		$this->assertEquals($expected,$logger->getLogLevelAsInt($level));
	}
}