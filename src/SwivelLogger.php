<?php
declare(strict_types=1);
/**
 * SwivelLogger.php
 *
 * @created       3/19/16
 * @version       1.0
 * @author        Dana Luther <dana.luther@gmail.com>
 * @yiiVersion    2.0.7
 */

namespace dhluther\swivel;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Yii;
use yii\helpers\VarDumper;
use yii\log\Logger;

/**
 * SwivelLogger
 *
 * This class acts as a mediator between the native Framework logging and the built-in Swivel library logging.
 */
class SwivelLogger extends AbstractLogger
{
    public string $category = 'application.swivel';

    public function __construct(string $category='application.swivel')
    {
    	$this->category = $category;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = [])
    {
        Yii::getLogger()->log($message . PHP_EOL . VarDumper::dumpAsString($context), $this->getLogLevelAsInt($level), $this->getLogCategory());
    }

    public function getLogCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setLogCategory(string $category)
    {
        $this->category = $category;
    }

    /**
     * Note: because it's casting as string, if a number is passed in, we need to handle those cases explicitly
     * @param string $level
     *
     * @return int
     */
    public function getLogLevelAsInt(string $level): int
    {
        switch ($level) {
	        case LogLevel::DEBUG:
	        case '8':
		        return Logger::LEVEL_TRACE;

	        case LogLevel::EMERGENCY:
	        case LogLevel::ERROR:
	        case LogLevel::CRITICAL:
	        case '1':
		        return Logger::LEVEL_ERROR;

	        case LogLevel::ALERT:
            case LogLevel::WARNING:
	        case '2':
                return Logger::LEVEL_WARNING;


            case LogLevel::INFO:
            case LogLevel::NOTICE:
	        case '4':
            default:
                return Logger::LEVEL_INFO;
        }
    }
}
