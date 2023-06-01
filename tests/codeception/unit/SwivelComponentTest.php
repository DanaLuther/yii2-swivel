<?php
/**
 * SwivelComponentTest.php
 *
 * @author Dana Luther <dana.luther@gmail.com>
 */

namespace dhluther\swivel\tests\unit;

use dhluther\swivel\SwivelComponent;
use dhluther\swivel\SwivelFeature;
use dhluther\swivel\SwivelLogger;
use dhluther\swivel\tests\Fixtures\SwivelFeatureFixture;
use yii\base\InvalidConfigException;
use Zumba\Swivel\Builder;

class SwivelComponentTest extends \Codeception\Test\Unit
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	public function _after()
	{
		\Yii::$container->set(SwivelComponent::class,[
			'loggerClass' => SwivelLogger::class
		]);
		\Yii::$container->set(SwivelComponent::class,[
			'userBucketProperty' => null,
		]);
	}

	public function testCreateSwivelComponent()
	{
		$component = \Yii::createObject(SwivelComponent::class);
		$this->assertInstanceOf(SwivelComponent::class, $component);
	}

	/**
	 * @depends testCreateSwivelComponent
	 */
	public function testCreateSwivelComponentWithBadUserProperty()
	{
		\Yii::$container->set(SwivelComponent::class,[
			'userBucketProperty' => 'bananaphone'
		]);
		$component = \Yii::createObject(SwivelComponent::class);
		$this->assertInstanceOf(SwivelComponent::class, $component);
		$this->assertEquals('bananaphone', $component->userBucketProperty);
	}

	/**
	 * @depends testCreateSwivelComponent
	 */
	public function testSetLogger()
	{
		$component = \Yii::createObject(SwivelComponent::class);
		$component->setLogger( new SwivelLogger() );
	}

	/**
	 * @depends testCreateSwivelComponent
	 */
	public function testCreateSwivelComponentWithBadLoggerClass()
	{
		\Yii::$container->set(SwivelComponent::class,[
			'loggerClass' => '\stdClass'
		]);
		$this->expectException(InvalidConfigException::class);
		$component = \Yii::createObject(SwivelComponent::class);
	}

	/**
	 * @depends testCreateSwivelComponent
	 */
	public function testReturnValue()
	{
		if (!extension_loaded('mysqli')){
			$this->markTestSkipped('No MySQL Support.');
		}
		$this->tester->haveFixtures([
			SwivelFeatureFixture::class,
		]);
		$this->tester->have(SwivelFeature::class,[
			'slug'=>'Sauce',
			'buckets'=>'1,2,3,4,5,6,7,8,9,10'
		]);
		$this->tester->have(SwivelFeature::class,[
			'slug'=>'Sauce.Spicy',
			'buckets'=>'1,2,3,4,5,6,7,8,9,10'
		]);
		$this->tester->have(SwivelFeature::class,[
			'slug'=>'Sauce.Mild',
			'buckets'=>''
		]);
		$swivel = \Yii::createObject(SwivelComponent::class);
		$this->assertTrue($swivel->returnValue('Sauce.Spicy', true, false));
		$this->assertFalse($swivel->returnValue('Sauce.Mild', true, false));
		$this->assertFalse($swivel->returnValue('Sauce.UnknownSpiceFactor', true, false));
	}

	/**
	 * @depends testCreateSwivelComponent
	 */
	public function testForFeature()
	{
		if (!extension_loaded('mysqli')){
			$this->markTestSkipped('No MySQL Support.');
		}
		$this->tester->haveFixtures([
			SwivelFeatureFixture::class,
		]);
		$this->tester->have(SwivelFeature::class,[
			'slug'=>'Sauce',
			'buckets'=>'1,2,3,4,5,6,7,8,9,10'
		]);
		$this->tester->have(SwivelFeature::class,[
			'slug'=>'Sauce.Spicy',
			'buckets'=>'1,2,3,4,5,6,7,8,9,10'
		]);
		$this->tester->have(SwivelFeature::class,[
			'slug'=>'Sauce.Mild',
			'buckets'=>''
		]);
		$swivel = \Yii::createObject(SwivelComponent::class);
		$builder = $swivel->forFeature('Sauce');
		$this->assertInstanceOf(Builder::class, $builder);
		$value = $builder
			->addValue('Mild', 'blue')
			->defaultValue( 'orange')
			->execute();
		$this->assertEquals('orange', $value);

		$value = $builder
			->addValue('Mild', 'blue')
			->addValue('Spicy', 'red')
			->defaultValue( 'orange')
			->execute();
		$this->assertEquals('red', $value);
	}


	/**
	 * @depends testCreateSwivelComponent
	 */
	public function testInvoke()
	{
		if (!extension_loaded('mysqli')){
			$this->markTestSkipped('No MySQL Support.');
		}
		$this->tester->haveFixtures([
			SwivelFeatureFixture::class,
		]);
		$this->tester->have(SwivelFeature::class,[
			'slug'=>'Sauce',
			'buckets'=>'1,2,3,4,5,6,7,8,9,10'
		]);
		$this->tester->have(SwivelFeature::class,[
			'slug'=>'Sauce.Spicy',
			'buckets'=>'1,2,3,4,5,6,7,8,9,10'
		]);
		$this->tester->have(SwivelFeature::class,[
			'slug'=>'Sauce.Mild',
			'buckets'=>''
		]);
		$swivel = \Yii::createObject(SwivelComponent::class);
		$this->assertTrue($swivel->invoke('Sauce.Spicy', fn()=>true));
		$this->assertNull($swivel->invoke('Sauce.Mild',  fn()=>true));
		$this->assertFalse($swivel->invoke('Sauce.UnknownSpiceFactor',  fn()=>true, fn()=>false));
	}
}