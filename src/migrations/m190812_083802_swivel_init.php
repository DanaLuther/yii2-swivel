<?php
/**
 * m190812_083802_log_init.php
 *
 * @created 8/12/19
 * @version 1.0
 * @author Dana Luther <dana.luther@gmail.com>
 */

use dhluther\swivel\SwivelComponent;
use yii\db\Migration;

/**
 * Initializes swivel table.
 *
 * If you are using multiple swivel components, you can extend this class and update the swivelComponent value so that
 * you can run the migration against all the required locations as you add them.  Alternately, you can set the
 * \dhluther\swivel\SwivelComponent::$autoCreateSwivelTable value to true and use that instead of the migration (not
 * recommended for production environments).
 *
 * ./yii migrate --migrationPath=@dhluther/swivel/migrations
 */
class m190812_083802_swivel_init extends Migration
{

	/**
	 * @var string The component that contains the configuration for swivel.
	 */
	public $swivelComponent = 'swivel';

	/**
	 * @var string The table that has been configured for use by the component. This will be pulled from the component
	 * configuration.
	 */
	protected $tableAlias;

	/**
	 * Initializes the migration.
	 * This method will set [[db]] to be the application's swivel component's db, if it is configured.
	 */
	public function init()
	{
		$this->initDbTarget();
		parent::init();
	}

	/**
	 * Check the configured SwivelComponent for the database settings required for the migration to be run
	 */
	protected function initDbTarget()
	{
		if (!$this->tableAlias)
		{
			$this->tableAlias = 'swivel';
			try
			{
				/** @var SwivelComponent $swivel */
				$swivel = \Yii::$app->{$this->swivelComponent};
				if (!is_a($swivel, SwivelComponent::class))
				{
					\Yii::warning('Failed to locate swivel component for migration - using defaults.', __METHOD__);

					return;
				}
				$this->db = $swivel->dbComponent;
				$this->tableAlias = $swivel->swivelTableAlias;
			}
			catch (\Exception $e)
			{
				\Yii::error('Failed to locate swivel component properties for migration - using defaults.', __METHOD__);
			}
		}
	}

	public function up()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			// http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable($this->tableAlias, [
			'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
			'slug' => 'MEDIUMTEXT',   // enable more than 254 chars for slug since they have . subfeatures
			'buckets' => 'TINYTEXT',  // 10 bucket system, so never more than 18 chars currently
			'INDEX ix_slug( slug(8) )',
		], $tableOptions);
	}

	public function down()
	{
		$this->dropTable($this->tableAlias);
	}
}
