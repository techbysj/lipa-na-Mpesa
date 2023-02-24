<?php

include_once __DIR__ . DIRECTORY_SEPARATOR."MpesaConfigExt.php";
include_once __DIR__ . DIRECTORY_SEPARATOR."flaircore/MpesaItem.php";
include_once __DIR__ . DIRECTORY_SEPARATOR."flaircore/stkPush.php";
include_once __DIR__ . DIRECTORY_SEPARATOR."flaircore/stkPushResponse.php";

class MpesaHelper
{

    private $passKey;
    private $consumerKey;
    private $consumerSecret;
    private $testmode;
    private $AccountReference;
    private $TransactionDesc;
    private $BusinessShortCode;

    public function __construct($passKey,$consumerKey,$consumerSecret,$AccountReference, $TransactionDesc, $BusinessShortCode, $testmode = false)
    {
        global $wpdb;
        $this->passKey = $passKey;
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->testmode = $testmode;
        $this->AccountReference = $AccountReference;
        $this->TransactionDesc = $TransactionDesc;
        $this->BusinessShortCode = $BusinessShortCode;
        $this->table = $wpdb->prefix. "mpesa_payment";
    }

    /**
     * @param \WC_Order $order
     * @param $callBackUrl
     * @return string|void
     */
    public function stkRequest(\WC_Order $order,$callBackUrl, $conversion_rate)
    {
        $currency = $order->get_currency();
        $phone = $order->get_billing_phone();
        $amount = (float) $order->get_total('edit');

        if ($currency != "KES"){
            $amount = ceil($amount * $conversion_rate);
            $order->add_order_note('Converting amount to Kenya Shillings KES '. $amount);
        }


        if (strlen(strval($phone)) < 12){
            $phone = "254" . strval((int)$phone);
        }elseif (strlen(strval($phone)) > 12){
            $phone = ltrim(strval($phone),"+");
        }else{
            $phone = strval($phone);
        }

        // Define our variables maybe
        $passKey = $this->passKey;
        $consumerKey = $this->consumerKey;
        $consumerSecret = $this->consumerSecret;

        $mpesaConfigs = new MpesaConfigExt();
        $mpesaConfigs->setConsumerKey($consumerKey);
        $mpesaConfigs->setConsumerSecret($consumerSecret);
        $mpesaConfigs->setPassKey($passKey);
        $mpesaConfigs->setEnviroment($this->testmode=='yes' ? 'sandbox' : 'live');

        $mpesaItem = new MpesaItem($mpesaConfigs);
        $mpesaItem->setBusinessShortCode('7314493');#Your business short code
        $mpesaItem->setTransactionType('CustomerBuyGoodsOnline');
        $mpesaItem->setAmount($amount);#Amount in Ksh
        $mpesaItem->setPartyA($phone); #2547********
        $mpesaItem->setPartyB('5311783');
        $mpesaItem->setPhoneNumber($phone);#2547********
        $mpesaItem->setCallBackURL($callBackUrl);
        $mpesaItem->setAccountReference($this->AccountReference);
        $mpesaItem->setTransactionDesc($this->TransactionDesc);

        $mpesaRequest = new stkPush($mpesaConfigs, $mpesaItem);
        $message = "";

        try {

            $request = $mpesaRequest->mpesaSTKPush();

            file_put_contents('mpesa.log',print_r( $mpesaItem,true). PHP_EOL,FILE_APPEND);

            $res = json_decode($request);

            $ResponseCode = $res->ResponseCode;

            if ($ResponseCode == "0"){
                $MerchantRequestID = $res->MerchantRequestID; //id
                $CheckoutRequestID = $res->CheckoutRequestID;

                $ResponseDescription = $res->ResponseDescription;
                $CustomerMessage = $res->CustomerMessage;
                $order->add_order_note( $CustomerMessage);

                //Create mpesa_payment and link to order
                $qr = $this->createMpesaPayment($order, $MerchantRequestID, $CheckoutRequestID, $ResponseDescription, $CustomerMessage);


                if ($qr){
                    $message = __("A payment prompt has been sent to Mpesa account ".$phone." Check your mobile phone to complete your payment.", 'wc_mpesa_xpress');
                }else{
                    $message = __("Failed to send MPesa request. Check you internet connection.", 'wc_mpesa_xpress');
                }
                $order->add_order_note( $message);

            }else{
                $message = __("Request not completed", "wc_mpesa_xpress");
                $order->add_order_note( $res->errorMessage);
            }

        } catch (\Throwable $error) {
            $message = __("STK push to ".$phone." Failed. Contact Site Administrator for further details.".$error->getMessage(), 'wc_mpesa_xpress');
        }

        return $message;
    }

    private function createMpesaPayment(\WC_Order $order, $MerchantRequestID, $CheckoutRequestID, $ResponseDescription, $CustomerMessage)
    {
        global $wpdb;
        $table = $this->table;

        $orderId = $order->get_id();
        $amount = $order->get_total();
        $status = "PENDING";

        $sql = "INSERT INTO `$table` (`order_id`, `MerchantRequestID`, `amount`, `status`, `CheckoutRequestID`, `ResponseDescription`, `CustomerMessage`) VALUES ( '$orderId', '$MerchantRequestID', '$amount', '$status','$CheckoutRequestID','$ResponseDescription','$CustomerMessage' )";

        return $wpdb->query($sql);

    }
}
