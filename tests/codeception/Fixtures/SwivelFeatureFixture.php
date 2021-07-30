<?php
/**
 * SwivelFeatureFixture.php
 *
 * @author Dana Luther <dana.luther@gmail.com>
 */

namespace dhluther\swivel\tests\Fixtures;


use dhluther\swivel\SwivelFeature;

class SwivelFeatureFixture extends \yii\test\ActiveFixture
{
	public $modelClass = SwivelFeature::class;
	public $dataFile = false; // empty data set
}