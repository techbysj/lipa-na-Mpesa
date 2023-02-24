<?php


include_once __DIR__ . DIRECTORY_SEPARATOR."flaircore/MpesaConfigs.php";

class MpesaConfigExt extends MpesaConfigs
{
    public function getConsumerKey(): string
    {
        return parent::getConsumerKey();
    }

    public function setConsumerKey(?string $consumerKey): void
    {
        parent::setConsumerKey($consumerKey);
    }

    public function getConsumerSecret(): string
    {
        return parent::getConsumerSecret();
    }

    public function setConsumerSecret(?string $consumerSecret): void
    {
        parent::setConsumerSecret($consumerSecret);
    }

    public function getEnviroment(): string
    {
        return parent::getEnviroment();
    }

    public function setEnviroment(?string $enviroment): void
    {
        parent::setEnviroment($enviroment);
    }

    public function getPassKey(): string
    {
        return parent::getPassKey();
    }

    public function setPassKey(?string $passKey): void
    {
        parent::setPassKey($passKey);
    }

    public function getTimestamp()
    {
        date_default_timezone_set('Africa/Nairobi');

        return date('YmdHis');//20180920204512 y,M,D,Hour,MIN,SEC
    }

}