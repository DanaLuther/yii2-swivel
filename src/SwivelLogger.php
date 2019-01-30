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

use Psr\Log\LogLevel;
use yii\log\Logger;

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
    public function log($level, $message, array $context = array())
    {
        return \Yii::getLogger()->log($message . PHP_EOL . \yii\helpers\VarDumper::dumpAsString($context), $this->getLogLevelAsInt($level), $this->getLogCategory());
    }

    public function getLogCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setLogCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @param string $level
     *
     * @return int
     */
    public function getLogLevelAsInt($level)
    {
        if (is_int($level)) {
            return $level;
        }

        switch ($level) {
            case LogLevel::DEBUG:
                return Logger::LEVEL_TRACE;

            case LogLevel::EMERGENCY:
            case LogLevel::ERROR:
            case LogLevel::CRITICAL:
                return Logger::LEVEL_ERROR;

            case LogLevel::ALERT:
            case LogLevel::WARNING:
                return Logger::LEVEL_WARNING;


            case LogLevel::INFO:
            case LogLevel::NOTICE:
            default:
                return Logger::LEVEL_INFO;
        }
    }

}
