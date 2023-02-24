<?php
/**
 * The Logging system for mpesa requests
 * @todo :: improve on this
 */

namespace Flaircore\Mpesa\Logger;


use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class MpesaLogger
{

    /**
     * @param $logFileName
     * @param $infoToLog
     */
    public function logResponse($logFileName, $infoToLog)
    {

        date_default_timezone_set('Africa/Dar_es_Salaam');
        // Create the logger
        $logger = new Logger('mpesa_logger');

        /**
         * Add some handlers
         * Check logs under /mpesa_request_logs in your project root dir
         */
        $logger->pushHandler(new StreamHandler(__DIR__.'../../../../../../mpesa_request_logs/'.$logFileName, Logger::DEBUG));
        $logger->pushHandler(new FirePHPHandler());

        // Use your logger and log the data i.e $infoToLog

        $logger->info($infoToLog);

        return $logger;
    }
}