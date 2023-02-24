
**INTRODUCTION**
This library focuses on making it easier for php developers to integrate mpesa payment system into their app, especially
if you're using a cms or a framework that let's you save/update your configs from the database. 

**INSTALLATION**
Make sure you have composer installed, then in the root of your project directory run 
`composer require flaircore/mpesa`

[Mpesa Online Payment Example](https://github.com/flaircore/php-mpesa/wiki/Home:)


**MAKING REQUESTS**


        # set the variables needed first (maybe)
        // access token
        
        $passKey = 'Your Pass key';
        $consumerKey = 'Your Consurer Key';
        $consumerSecret = 'Your Consumer Secret';

        $mpesaConfigs = new \Flaircore\Mpesa\MpesaConfigs();
        $mpesaConfigs->setConsumerKey($consumerKey);
        $mpesaConfigs->setConsumerSecret($consumerSecret);
        $mpesaConfigs->setPassKey($passKey);
        $mpesaConfigs->setEnviroment('sandbox'); #sandbox or live; TODO work on request urls

        $mpesaItem = new \Flaircore\Mpesa\MpesaItem($mpesaConfigs);
        $mpesaItem->setBusinessShortCode('Your Bs shortcode');
        $mpesaItem->setTransactionType('CustomerPayBillOnline');
        $mpesaItem->setAmount('Amount in KSH');
        $mpesaItem->setPartyA('PartyA'); #2547******00
        $mpesaItem->setPartyB($mpesaItem->getBusinessShortCode());
        $mpesaItem->setPhoneNumber('2547******00');
        $mpesaItem->setCallBackURL('call back url string where to receive the response');
        $mpesaItem->setAccountReference('Product Title');
        $mpesaItem->setTransactionDesc('maybe Product EntityID 111');

        $mpesaRequest = new \Flaircore\Mpesa\Requests\stkPush($mpesaConfigs, $mpesaItem);#pass the mpesaConfigs and mpesaItem in that order
        
        #var_dump($mpesaRequest->mpesaSTKPush());die;
        $mpesaRequest->mpesaSTKPush();#send the request
        
**PREREQUISITES**
PHP 7.1 or above, also
curl and json ext must be enabled.

**Contributing**
Seeking co-contributers to make this library whole and better
please check the todo.php in this directory

**Credits**
Inspired by `https://github.com/bnjunge/MPESA-API-Tutorial`
