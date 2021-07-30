<?php
declare(strict_types=1);
/**
 * SwivelComponent.php
 *
 * @created       3/19/16
 * @version       1.0
 * @author        Dana Luther <dana.luther@gmail.com>
 * @yiiVersion    2.0.7
 */

namespace dhluther\swivel;

use Exception;
use Psr\Log\LoggerInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\helpers\ArrayHelper;
use Zumba\Swivel\Builder;

/**
 * Class SwivelComponent
 * @package dhluther\swivel
 * @property-read SwivelLogger $logger
 */
class SwivelComponent extends yii\base\BaseObject
{

    /**
     * @var ?SwivelLoader
     */
    protected ?SwivelLoader $loader=null;

    /**
     * @var array Options to be passed to the Config
     */
    public array $options = [];

    /**
     * @var bool Whether to create the swivel table automatically when it does not exist
     *
     * This value has been kept for backward compatibility and is not recommended for production environments. Use the
     * provided migration to initialize the table:
     *
     * ./yii migrate --migrationPath=@dhluther/swivel/migrations
     * @deprecated
     */
    public bool $autoCreateSwivelTable = false;

    /**
     * @var string The component alias being used -- allows for multiple swivel installations in a single application
     */
    public string $componentAlias = 'swivel';

    /**
     * @var string The table name to be used to store the swivel features and associated buckets
     */
    public string $swivelTableAlias = 'swivel';
    /**
     * @var string The Application component ID for the swivel database connection
     */
    public string $dbComponent = 'db';

    /**
     * @var string The class name for the model holding the swivel map data
     */
    public string $modelClass = 'dhluther\swivel\SwivelFeature';

    /**
     * @var string The class name for the model interfacing with the swivel native logger
     */
    public string $loggerClass = 'dhluther\swivel\SwivelLogger';

    /**
     * @var string The category that messages should be logged to - will be passed to the Logger
     * @deprecated set Logger category via DI:
     * e.g.,
     *      Yii::$container->set(SwivelLogger::class, ['category'=>'application.swivel']);
     *
     */
    public string $loggerCategory = 'application.swivel';

    /**
     * @var string The default Cookie to store the swivel bucket information for the user
     */
    public string $cookieName = 'Swivel_Bucket';
    /**
     * The name of the property on the application user model that holds their assigned bucket identifier
     * If set to null, no property on the web user model will be examined
     * @var null|string
     */
    public ?string $userBucketProperty = null;
    /**
     * @var null|int The default bucket ID - if null, one will be randomly generated and assigned
     */
    public ?int $bucketIndex = null;

    /**
     * This can be any callable - defaults to mt_rand(1,10)
     *
     * @var string|callable The function or method to be called to generate the bucket index
     */
    public string $generatorCallable = 'mt_rand';
    /**
     * @var array Arguments to be passed to the generator
     */
    public array $generatorArgs = [1, 10];

    /**
     * @var ?LoggerInterface
     */
    protected ?LoggerInterface $_logger = null;

    public function init()
    {
        Yii::debug('Initializing Swivel Component.', __METHOD__);
        parent::init();

        // @codeCoverageIgnoreStart
        if ($this->autoCreateSwivelTable) {
           Yii::error('Auto creation of swivel tables is no longer supported', __METHOD__);
        }
        // @codeCoverageIgnoreEnd

        // If we have a registered user, assume they should have some property or magic method that holds their bucket ID
        if ($this->userBucketProperty) {
        	try
	        {
		        $this->bucketIndex = Yii::$app->user->{$this->userBucketProperty};
	        } catch (\Exception $e)
	        {
	        	Yii::error('Failed to locate user property for bucket index: '.$this->userBucketProperty, __METHOD__);
	        }
        }
        // If no bucket has been selected, establish a cookie for the bucket identifier and use that for the duration
        // of the user's time on site - otherwise they will get a different bucket with each page load
        if (!$this->bucketIndex) {
            $this->bucketIndex = $this->checkAndApplyIndex();
        }

        $this->loader = new SwivelLoader(ArrayHelper::merge($this->getDefaultOptions(), $this->options));
    }


    /**
     * @param $slug
     *
     * @return Builder
     */
    public function forFeature($slug): Builder
    {
        return $this->loader->getManager()->forFeature($slug);
    }

    /**
     * Syntactic sugar for creating simple feature toggles (ternary style)
     *
     * @param string $slug
     * @param mixed $a
     * @param mixed $b
     * @return mixed
     */
    public function invoke(string $slug, $a, $b = null)
    {
        return $this->loader->getManager()->invoke($slug, $a, $b);
    }


    /**
     * Shorthand syntactic sugar for invoking a simple feature behavior using Builder::addValue.
     * Useful for ternary style code.
     *
     * @param string $slug
     * @param $a
     * @param null $b
     *
     * @return mixed
     */
    public function returnValue(string $slug, $a, $b = null)
    {
        return $this->loader->getManager()->returnValue($slug, $a, $b);
    }

    /**
     * Default configuration options for the Loader
     *
     * @return array
     */
    protected function getDefaultOptions(): array
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
    protected function checkAndApplyIndex(): ?int
    {
        if (!Yii::$app->session->has($this->cookieName)) {
            Yii::$app->session->set($this->cookieName, $this->defaultBucketGenerator());
            $this->getLogger()->debug('Set default bucket value for new user.', ['Bucket ID' => Yii::$app->session->get($this->cookieName)]);
        }

        return Yii::$app->session->get($this->cookieName);
    }

    /**
     * Generate the random bucket for the user
     * Override as necessary if updating the configuration options is not sufficient
     *
     * @return int
     */
    public function defaultBucketGenerator(): int
    {
        return call_user_func_array($this->generatorCallable, $this->generatorArgs);
    }

    /**
     * @param Connection $db
     * @param string $tableName
     * @throws Exception
     * @deprecated This method is going away as it has been replaced by the migration.
     * @codeCoverageIgnore
     */
    protected function initSwivelTable($db, $tableName)
    {
        Yii::debug('Creating Swivel Feature Table', __METHOD__);
        $db->createCommand()->createTable($tableName, [
                'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
                'slug' => 'MEDIUMTEXT',   // enable more than 254 chars for slug since they have . subfeatures
                'buckets' => 'TINYTEXT',  // 10 bucket system, so never more than 18 chars currently
                'INDEX ix_slug( slug(8) )',
            ])->execute();
    }

	/**
	 * Default Log option -- can be overridden by passing a different logger through the config, or by extending this
	 * class and overriding the method
	 *
	 * @return LoggerInterface
	 * @throws InvalidConfigException
	 */
    protected function getDefaultLogger(): LoggerInterface
    {
	    $logger = Yii::createObject($this->loggerClass);
	    if (!$logger instanceof LoggerInterface)
	    {
	    	throw new InvalidConfigException('Configured logger must implement the LoggerInterface class.');
	    }
        return $logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->_logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        if (!$this->_logger) {
            $this->_logger = $this->getDefaultLogger();
        }
        return $this->_logger;
    }

	/**
	 * Automatic creation of swivel component on demand if one is not already configured for the application
	 *
	 * @param string $componentAlias
	 * @return SwivelComponent
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function loadSwivel(string $componentAlias = 'swivel'): SwivelComponent
	{
		if (!Yii::$app->has($componentAlias))
		{
			Yii::$app->set($componentAlias, Yii::createObject(SwivelComponent::class));
		}
		return Yii::$app->get($componentAlias);
	}
}
