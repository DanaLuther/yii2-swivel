<?php
/**
 * InitMigrationTest.php
 *
 * @author Dana Luther <dana.luther@gmail.com>
 */

namespace dhluther\swivel\tests\unit;

use dhluther\swivel\migrations\m190812_083802_swivel_init;
use dhluther\swivel\SwivelComponent;

class InitMigrationTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testMigration()
    {
		if (!extension_loaded('mysqli')){
			$this->markTestSkipped('No MySQL Support.');
		}
		$migration = new m190812_083802_swivel_init();
		$migration->down();
	    $this->assertEquals(0,\Yii::$app->db->createCommand("SHOW TABLES LIKE 'swivel'")->execute());
		$migration->up();
	    $this->assertEquals(1,\Yii::$app->db->createCommand("SHOW TABLES LIKE 'swivel'")->execute());
    }

    public function testMigrationAlternateTable()
    {
	    if (!extension_loaded('mysqli')){
		    $this->markTestSkipped('No MySQL Support.');
	    }
    	\Yii::$app->set('swivel', \Yii::createObject(SwivelComponent::class));
    	\Yii::$app->swivel->swivelTableAlias = 'alt_swivel';
		$migration = new m190812_083802_swivel_init();
	    $migration->up();
	    $this->assertEquals(1,\Yii::$app->db->createCommand("SHOW TABLES LIKE 'alt_swivel'")->execute());
	    $migration->down();
	    $this->assertEquals(0,\Yii::$app->db->createCommand("SHOW TABLES LIKE 'alt_swivel'")->execute());
    }
}