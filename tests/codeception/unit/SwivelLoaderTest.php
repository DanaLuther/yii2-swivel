<?php
/**
 * SwivelLoaderTest.php
 *
 * @author Dana Luther <dana.luther@gmail.com>
 */

namespace dhluther\swivel\tests\unit;

use dhluther\swivel\SwivelLoader;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;

class SwivelLoaderTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testCreateSwivelLoader()
    {
		$loader = \Yii::createObject(SwivelLoader::class);
		$this->assertInstanceOf(SwivelLoader::class, $loader);
    }

	/**
	 * @depends testCreateSwivelLoader
	 */
    public function testInvalidModelAlias()
    {
    	$this->expectException(InvalidConfigException::class);
		$loader = \Yii::createObject(SwivelLoader::class, [
			'options'=>['ModelAlias'=>'\stdClass']
		]);
		$loader->setBucketIndex(1);
	    $this->assertNull($loader->getManager());
    }

	/**
	 * @depends testCreateSwivelLoader
	 */
    public function testInvalidBucket()
    {
    	$this->expectException(InvalidArgumentException::class);
		$loader = \Yii::createObject(SwivelLoader::class);
		$loader->setBucketIndex(1000);
    }

	/**
	 * @depends testCreateSwivelLoader
	 */
    public function testInvalidBucketAlpha()
    {
    	$this->expectError();
		$loader = \Yii::createObject(SwivelLoader::class);
		$loader->setBucketIndex('Orange');
    }

	/**
	 * @depends testCreateSwivelLoader
	 */
	public function testAddBucketToManager()
	{
		$loader = \Yii::createObject(SwivelLoader::class);
		$manager = $loader->getManager();
		$loader->setBucketIndex(2);
	}
	/**
	 * @depends testCreateSwivelLoader
	 */
	public function testConfigMetrics()
	{
		// Metrics needs to be of type MetricsInterface, so this will throw a TypeError
		$this->expectError();
		$loader = \Yii::createObject(SwivelLoader::class ,[
			'options'=>['Metrics'=>'\stdClass']
		]);
		$config = $loader->getConfig();
	}
}