<?php
/**
 * SwivelFeatureTest.php
 *
 * @author Dana Luther <dana.luther@gmail.com>
 */

namespace dhluther\swivel\tests\unit;

use Codeception\Attribute\Depends;
use Codeception\Attribute\Group;
use dhluther\swivel\SwivelFeature;
use dhluther\swivel\tests\Fixtures\SwivelFeatureFixture;

class SwivelFeatureTest extends \Codeception\Test\Unit
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

    #[Group('mysql')]
	public function testCreateSwivelFeature()
	{
		if (!extension_loaded('mysqli')){
			$this->markTestSkipped('No MySQL Support.');
		}
		$this->tester->haveFixtures([
			SwivelFeatureFixture::class,
		]);
		$feature = new SwivelFeature();
		$this->assertFalse($feature->save());
		$this->assertNull($feature->getBucketData());
		$this->assertEquals([],$feature->getMapData());
		$feature->slug = 'Sauce';
		$feature->buckets = '1,2,3,4,5,6,7,8,9,10';
		$this->assertTrue($feature->save());
		$this->assertIsArray($feature->getBucketData());
	}

    #[Group('mysql')]
	public function testBucketValidationWithSingleDigit()
	{
		if (!extension_loaded('mysqli')){
			$this->markTestSkipped('No MySQL Support.');
		}
		$this->tester->haveFixtures([
			SwivelFeatureFixture::class,
		]);
		$feature = $this->tester->make(SwivelFeature::class, [
			'slug' => 'Sauce',
		]);
		$this->assertTrue($feature->save());
	}

    #[Group('mysql')]
	public function testMapData()
	{
		if (!extension_loaded('mysqli')){
			$this->markTestSkipped('No MySQL Support.');
		}
		$this->tester->haveFixtures([
			SwivelFeatureFixture::class,
		]);
		$feature = $this->tester->have(SwivelFeature::class, [
			'slug'    => 'Sauce',
			'buckets' => '1,2,3,4,5,6'
		]);
		$map = $feature->getMapData();
		$this->assertCount(6, $map['Sauce']);
	}
}
