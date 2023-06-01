<?php
/**
 * SwivelLoaderTest.php
 *
 * @author Dana Luther <dana.luther@gmail.com>
 */

namespace dhluther\swivel\tests\unit;

use Codeception\Attribute\Depends;
use Codeception\Attribute\Group;
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

    #[Depends('testCreateSwivelLoader')]
    public function testInvalidModelAlias()
    {
    	$this->expectException(InvalidConfigException::class);
		$loader = \Yii::createObject(SwivelLoader::class, [
			'options'=>['ModelAlias'=>'\stdClass']
		]);
		$loader->setBucketIndex(1);
	    $this->assertNull($loader->getManager());
    }

    #[Depends('testCreateSwivelLoader')]
    public function testInvalidBucket()
    {
    	$this->expectException(InvalidArgumentException::class);
		$loader = \Yii::createObject(SwivelLoader::class);
		$loader->setBucketIndex(1000);
    }

    #[Depends('testCreateSwivelLoader')]
    public function testInvalidBucketAlpha()
    {
		$errorFound = false;
		try {
			$loader = \Yii::createObject(SwivelLoader::class);
			$loader->setBucketIndex('Orange');
		} catch (\Error $e){
			$errorFound = true;
		}
		$this->assertTrue($errorFound, 'Never threw the expected error.');
    }

    #[Depends('testCreateSwivelLoader')]
    #[Group('mysql')]
	public function testAddBucketToManager()
	{
		if (!extension_loaded('mysqli')){
			$this->markTestSkipped('No MySQL Support.');
		}
		$loader = \Yii::createObject(SwivelLoader::class);
		$manager = $loader->getManager();
		$loader->setBucketIndex(2);
	}

    #[Depends('testCreateSwivelLoader')]
    #[Group('mysql')]
	public function testConfigMetrics()
	{
		if (!extension_loaded('mysqli')){
			$this->markTestSkipped('No MySQL Support.');
		}
		// Metrics needs to be of type MetricsInterface, so this will throw a TypeError
		$errorFound = false;
		try {
			$loader = \Yii::createObject(SwivelLoader::class, [
				'options' => ['Metrics' => '\stdClass']
			]);
			$config = $loader->getConfig();
		} catch ( \Error $e){
			$errorFound = true;
		}
		$this->assertTrue($errorFound, 'Failed to throw TypeError for MetricsInterface.');
	}
}
