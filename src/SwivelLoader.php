<?php
/**
 * SwivelLoader.php
 *
 * @created       3/19/16
 * @version       1.0
 * @author        Dana Luther <dana.luther@gmail.com>
 * @yiiVersion    2.0.7
 */

namespace dhluther\swivel;


class SwivelLoader 
{
	/**
	 * Swivel config
	 *
	 * @var \Zumba\Swivel\Config
	 */
	protected $config;
	/**
	 * Configuration options
	 *
	 * @var array
	 */
	protected $options;
	/**
	 * Swivel manager
	 *
	 * @var \Zumba\Swivel\Manager
	 */
	protected $manager;

	/**
	 * SwivelLoader only creates the swivel manager whenever you try to use it.
	 *
	 * @param array $options
	 */
	public function __construct( $options )
	{
		$this->options = $options;
	}

	/**
	 * Get the swivel config instance
	 *
	 * @return \Zumba\Swivel\Config
	 */
	public function getConfig()
	{
		if ( empty($this->config) )
		{
			$options = $this->options;
			$this->config = new \Zumba\Swivel\Config(
				$this->getModel()->getMapData(),
				$options[ 'BucketIndex' ],
				$options[ 'Logger' ]
			);
			if ( !empty($options[ 'Metrics' ]) )
			{
				$this->config->setMetrics( $options[ 'Metrics' ] );
			}
		}

		return $this->config;
	}

	/**
	 * Get the swivel manager instance
	 *
	 * @return \Zumba\Swivel\Manager
	 */
	public function getManager()
	{
		return $this->manager ? : $this->load();
	}

	/**
	 * Get the configured swivel model.
	 *
	 * Falls back to the SwivelFeature model provided by the plugin if the app does not define one.
	 *
	 * @return SwivelFeature
	 */
	protected function getModel()
	{
		//return SwivelFeature::model( $this->options[ 'ModelAlias' ] );
		return SwivelFeature::find();
	}

	/**
	 * Create a Swivel Manager object and return it.
	 *
	 * @return \Zumba\Swivel\Manager
	 */
	protected function load()
	{
		$this->manager = new \Zumba\Swivel\Manager( $this->getConfig() );

		return $this->manager;
	}

	/**
	 * Used to set the bucket index before loading swivel.
	 *
	 * @param integer $index Number between 1 and 10
	 *
	 * @return void
	 * @throws \InvalidArgumentException if $index is not valid
	 */
	public function setBucketIndex( $index )
	{
		if ( !is_numeric( $index ) || $index < 1 || $index > 10 )
		{
			throw new \InvalidArgumentException( "$index is not a valid bucket index." );
		}
		if ( empty($this->manager) )
		{
			$this->options[ 'BucketIndex' ] = $index;
		} else
		{
			$config = $this->getConfig();
			$config->setBucketIndex( $index );
			$this->manager->setBucket( $config->getBucket() );
		}
	}
}