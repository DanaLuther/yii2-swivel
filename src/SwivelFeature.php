<?php
declare(strict_types=1);
/**
 * SwivelFeature.php
 *
 * @created       3/19/16
 * @version       1.0
 * @author        Dana Luther <dana.luther@gmail.com>
 * @yiiVersion    2.0.7
 */

namespace dhluther\swivel;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

/**
 * SwivelFeature
 *
 * The following are magic properties of the model, as established via ActiveRecord from the database
 * @property string $slug
 * @property string $buckets
 */
class SwivelFeature extends ActiveRecord implements SwivelDataSource
{
    const DELIMITER = ',';

    public static string $componentAlias = 'swivel';

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
	    $component = SwivelComponent::loadSwivel(self::$componentAlias);
        return $component->swivelTableAlias ?? 'swivel';
    }

    /**
     * @return Connection the database connection used by this AR class.
     * @throws InvalidConfigException
     */
    public static function getDb(): Connection
    {
	    $component = SwivelComponent::loadSwivel(self::$componentAlias);
        return Yii::$app->get($component->dbComponent) ?? Yii::$app->getDb();
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['slug'], 'required'],
            [['buckets'], 'string', 'max' => 254],
            [['slug'], 'string'],
        ];
    }

    public function getBucketData(): ?array
    {
        if (!$this->buckets || $this->buckets == '') {
            return null;
        }
        return explode(self::DELIMITER, $this->buckets);
    }

    public function beforeValidate()
    {
    	if (is_numeric($this->buckets))
	    {
	    	$this->buckets = (string)$this->buckets;
	    }
	    return parent::beforeValidate();
    }

    /**
     * Format data from the active record to the data swivel expects
     *
     * @return array
     */
    protected function formatRow(): array
    {
        return [$this->slug => $this->getBucketData()];
    }

    /**
     * Return an array of map data in the format that Swivel expects
     *
     * @return array
     */
    public function getMapData(): array
    {
        /** @var SwivelFeature[] $data */
        $data = self::find()->all();
        if (empty($data)) {
            return [];
        }
        $map = [];
        foreach ($data as $row) {
            $map = ArrayHelper::merge($map, $row->formatRow());
        }
        return $map;
    }
}
