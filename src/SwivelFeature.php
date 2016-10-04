<?php
/**
 * SwivelFeature.php
 *
 * @created       3/19/16
 * @version       1.0
 * @author        Dana Luther <dana.luther@gmail.com>
 * @yiiVersion    2.0.7
 */

namespace dhluther\swivel;


class SwivelFeature extends \yii\db\ActiveRecord
{
	const DELIMITER = ',';
		
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return \Yii::$app->swivel->swivelTableAlias;
	}
	
	/**
	 * @return \yii\db\Connection the database connection used by this AR class.
	 */
	public static function getDb()
	{
		return \Yii::$app->get( \Yii::$app->swivel->dbComponent );
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[ [ 'slug'], 'required'],
			[ [ 'buckets' ], 'string', 'max' => 254 ],
		];
	}

	public function getBucketData()
	{
		if ( !$this->buckets || $this->buckets == '' )
			return null;
		return explode( self::DELIMITER, $this->buckets );
	}

	/**
	 * Format data from the active record to the data swivel expects
	 *
	 * @return array
	 */
	protected function formatRow(  )
	{
		return [ $this->slug => $this->getBucketData() ];
	}

	/**
	 * Return an array of map data in the format that Swivel expects
	 *
	 * @return array
	 */
	public function getMapData()
	{
		/** @var SwivelFeature[] $data */
		$data = SwivelFeature::find()->all();
		if ( empty( $data ))
		{
			return[];
		}
		$map = [];
		foreach( $data as $row )
		{
			$map = \yii\helpers\ArrayHelper::merge($map, $row->formatRow() );
		}
		return $map;
	}
}
