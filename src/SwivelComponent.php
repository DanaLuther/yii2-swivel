<?php
/**
 * SwivelComponent.php
 *
 * @created       3/19/16
 * @version       1.0
 * @author        Dana Luther <dana.luther@gmail.com>
 * @yiiVersion    2.0.7
 */

namespace dhluther\swivel;

use Yii;

class SwivelComponent extends \yii\base\Object {

	/**
	 * @var SwivelLoader
	 */
	protected $loader;
	/**
	 * @var array Options to be passed to the Config
	 */
	public $options = [];

	/**
	 * @var bool Whether to create the swivel table automatically when it does not exist
	 */
	public $autoCreateSwivelTable = true;
	/**
	 * @var string The table name to be used to store the swivel features and associated buckets
	 */
	public $swivelTableAlias = 'swivel';
	/**
	 * @var string The Application component ID for the swivel database connection
	 */
	public $dbComponent = 'db';

	/**
	 * @var string The class name for the model holding the swivel map data
	 */
	public $modelClass = 'SwivelFeature';

	/**
	 * @var string The default Cookie to store the swivel bucket information for the user
	 */
	public $cookieName = 'Swivel_Bucket';
	/**
	 * The name of the property on the application user model that holds their assigned bucket identifier
	 * If set to null, no property on the web user model will be examined
	 * @var null|string
	 */
	public $userBucketProperty = null;
	/**
	 * @var null|int The default bucket ID - if null, one will be randomly generated and assigned
	 */
	public $bucketIndex = null;

	/**
	 * This can be any callable - defaults to mt_rand(1,10)
	 *
	 * @var callable The function or method to be called to generate the bucket index
	 */
	public $generatorCallable = "mt_rand";
	/**
	 * @var array Arguments to be passed to the generator
	 */
	public $generatorArgs = [1,10];

	/**
	 * @var \Psr\Log\LoggerInterface
	 */
	protected $_logger;

	public function init()
	{
		parent::init();

		if ( $this->autoCreateSwivelTable )
		{
			/** @var \yii\db\Connection $db */
			$db = Yii::$app->{$this->dbComponent};
			try {
				$db->createCommand()->delete( $this->swivelTableAlias, '0=1');
			} catch ( \Exception $e )
			{
				$this->initSwivelTable( $db, $this->swivelTableAlias );
			}
		}

		// If we have a registered user, assume they should have some property or magic method that holds their bucket ID
		if ( $this->userBucketProperty )
		{
			$this->bucketIndex = Yii::$app->user->{$this->userBucketProperty};
		}
		// If no bucket has been selected, establish a cookie for the bucket identifier and use that for the duration
		// of the user's time on site - otherwise they will get a different bucket with each page load
		if ( !$this->bucketIndex )
		{
			$this->bucketIndex = $this->checkAndApplyIndex();
		}

		$this->loader = new SwivelLoader( \yii\helpers\ArrayHelper::merge( $this->getDefaultOptions(), $this->options ));
	}


	/**
	 * @param $slug
	 *
	 * @return \Zumba\Swivel\Builder
	 */
	public function forFeature( $slug )
	{
		return $this->loader->getManager()->forFeature( $slug );
	}

	/**
	 * Syntactic sugar for creating simple feature toggles (ternary style)
	 *
	 * @param string $slug
	 * @param mixed $a
	 * @param mixed $b
	 * @return mixed
	 */
	public function invoke($slug, $a, $b = null) {
		return $this->loader->getManager()->invoke($slug, $a, $b);
	}


	/**
	 * Shorthand syntactic sugar for invoking a simple feature behavior using Builder::addValue.
	 * Useful for ternary style code.
	 *
	 * @param $slug
	 * @param $a
	 * @param null $b
	 *
	 * @return mixed
	 */
	public function returnValue( $slug, $a, $b=null ) {
		return $this->loader->getManager()->returnValue($slug, $a, $b );
	}

	/**
	 * Default configuration options for the Loader
	 *
	 * @return array
	 */
	protected function getDefaultOptions()
	{
		return [
			'BucketIndex' => $this->bucketIndex,
			'LoaderAlias' => 'SwivelLoader',
			'Logger' => $this->getLogger(),
			'Metrics' => null,
			'ModelAlias' => $this->modelClass,
		];
	}

	/**
	 * Check the user state for a bucket index, and if set, return it.
	 * @return int
	 */
	protected function checkAndApplyIndex()
	{
		if ( !Yii::$app->user->hasState( $this->cookieName ))
		{
			Yii::$app->user->setState( $this->cookieName,  $this->defaultBucketGenerator() );
			$this->getLogger()->debug( 'Set default bucket value for new user.', [ 'Bucket ID' => Yii::$app->user->getState( $this->cookieName ) ] );
		}
		return Yii::$app->user->getState( $this->cookieName );
	}

	/**
	 * Generate the random bucket for the user
	 * Override as necessary if updating the configuration options is not sufficient
	 *
	 * @return int
	 */
	public function defaultBucketGenerator()
	{
		return call_user_func_array( $this->generatorCallable, $this->generatorArgs );
	}

	/**
	 * @param \yii\db\Connection $db
	 * @param string $tableName
	 */
	protected function initSwivelTable( $db, $tableName  )
	{
		$db->createCommand()->createTable($tableName, [
				'id'=>'INT PRIMARY KEY AUTO_INCREMENT',
				'slug'=>'MEDIUMTEXT',   // enable more than 254 chars for slug since they have . subfeatures
				'buckets'=>'TINYTEXT',  // 10 bucket system, so never more than 18 chars currently
				'INDEX ix_slug( slug(8) )',
			]
		);
	}
	/**
	 * Default Log option -- can be overridden by passing a different logger through the config, or by extending this
	 * class and overriding the method
	 *
	 * @return SwivelLogger
	 */
	protected function getDefaultLogger()
	{
		return new SwivelLogger();
	}
	/**
	 * @param \Psr\Log\LoggerInterface $logger
	 */
	public function setLogger( \Psr\Log\LoggerInterface $logger )
	{
		$this->_logger = $logger;
	}

	/**
	 * @return \Psr\Log\LoggerInterface
	 */
	public function getLogger()
	{
		if ( !$this->_logger )
		{
			$this->_logger = $this->getDefaultLogger();
		}
		return $this->_logger;
	}
}