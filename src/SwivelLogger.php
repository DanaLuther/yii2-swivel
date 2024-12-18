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

    public function __construct(string $category = 'application.swivel')
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
    public function log($level, $message, array $context = []): void
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
    public function setLogCategory(string $category): void
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
        return match ($level) {
            LogLevel::DEBUG, '8' => Logger::LEVEL_TRACE,
            LogLevel::EMERGENCY, LogLevel::ERROR, LogLevel::CRITICAL, '1' => Logger::LEVEL_ERROR,
            LogLevel::ALERT, LogLevel::WARNING, '2' => Logger::LEVEL_WARNING,
            default => Logger::LEVEL_INFO,
        };
    }
}
