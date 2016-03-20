<?php
/**
 * SwivelLogger.php
 *
 * @created       3/19/16
 * @version       1.0
 * @author        Dana Luther <dana.luther@gmail.com>
 * @yiiVersion    2.0.7
 */

namespace dhluther\swivel;

class SwivelLogger extends \Psr\Log\AbstractLogger
{
	public $category = 'application.swivel';

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed $level
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function log($level, $message, array $context = array()){
		return \Yii::getLogger()->log( $message.PHP_EOL.\yii\helpers\VarDumper::dumpAsString($context), $level , $this->category );
	}

}