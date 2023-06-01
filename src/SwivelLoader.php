<?php
declare(strict_types=1);
/**
 * SwivelLoader.php
 *
 * @created       3/19/16
 * @version       1.0
 * @author        Dana Luther <dana.luther@gmail.com>
 * @yiiVersion    2.0.7
 */

namespace dhluther\swivel;

use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use Zumba\Swivel\Config;
use Zumba\Swivel\Manager;

class SwivelLoader
{
    /**
     * Swivel config
     *
     * @var Config
     */
    protected Config $config;

    /**
     * Configuration options
     *
     * @var array
     */
    protected array $options;

    /**
     * Swivel manager
     *
     * @var ?Manager
     */
    protected ?Manager $manager=null;

    /**
     * SwivelLoader only creates the swivel manager whenever you try to use it.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * Get the swivel config instance
     *
     * @return Config
     * @throws InvalidConfigException
     */
    public function getConfig(): Config
    {
        if (empty($this->config)) {
            $options = $this->options;
            $this->config = new Config(
                $this->getModel()->getMapData(),
                $options['BucketIndex'] ?? rand(1, 10),
                $options['Logger'] ?? \Yii::createObject(SwivelLogger::class)
            );
            if (!empty($options['Metrics'])) {
                $this->config->setMetrics($options['Metrics']);
            }
        }

        return $this->config;
    }

    /**
     * Get the swivel manager instance
     *
     * @return Manager
     * @throws InvalidConfigException
     */
    public function getManager(): Manager
    {
        return $this->manager ?: $this->load();
    }

    /**
     * Get the configured swivel model.
     *
     * Falls back to the SwivelFeature model provided by the plugin if the app does not define one.
     *
     * @return SwivelDataSource
     * @throws InvalidConfigException
     */
    protected function getModel(): SwivelDataSource
    {
        $model = \Yii::createObject($this->options['ModelAlias'] ?? SwivelFeature::class);
        if (!$model instanceof SwivelDataSource) {
            throw new InvalidConfigException('Configured model must implement SwivelDataSource');
        }
        return $model;
    }

    /**
     * Create a Swivel Manager object and return it.
     *
     * @return Manager
     * @throws InvalidConfigException
     */
    protected function load(): Manager
    {
        $this->manager = new Manager($this->getConfig());

        return $this->manager;
    }

    /**
     * Used to set the bucket index before loading swivel.
     *
     * @param int $index Number between 1 and 10
     *
     * @throws InvalidArgumentException|InvalidConfigException
     */
    public function setBucketIndex(int $index)
    {
        if ($index < 1 || $index > 10) {
            throw new InvalidArgumentException("$index is not a valid bucket index.");
        }
        if (empty($this->manager)) {
            $this->options['BucketIndex'] = $index;
        } else {
            $config = $this->getConfig();
            $config->setBucketIndex($index);
            $this->manager->setBucket($config->getBucket());
        }
    }
}
