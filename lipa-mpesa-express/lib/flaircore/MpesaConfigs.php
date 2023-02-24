<?php
/**
 * Defines/sets the required configuration details/data
 * required by the mpesa api requests
 */

class MpesaConfigs
{

    private $consumerKey;

    private $consumerSecret;

    private $enviroment;

    private $passKey;

    private $Timestamp;

    public function getConsumerKey(): string
    {
        return $this->consumerKey;
    }

    public function setConsumerKey(?string $consumerKey): void
    {
        $this->consumerKey = $consumerKey;
    }


    public function getConsumerSecret(): string
    {
        return $this->consumerSecret;
    }

    public function setConsumerSecret(?string $consumerSecret): void
    {
        $this->consumerSecret = $consumerSecret;
    }

    public function getEnviroment(): string
    {

        return $this->enviroment;
    }

    public function setEnviroment(?string $enviroment): void
    {
        $this->enviroment = $enviroment;
    }

    public function getPassKey(): string
    {
        return $this->passKey;
    }

    public function setPassKey(?string $passKey): void
    {
        $this->passKey = $passKey;
    }

    /**
     * @return the timestamp
     * set explicitly and return
     */
    public function getTimestamp()
    {
        date_default_timezone_set('Africa/Dar_es_Salaam');

        return date('YmdGis');//20180920204512 y,M,D,Hour,MIN,SEC
    }


}
