<?php
/*
Plugin Name: Mpesa Xpress Woocommerce
Description: Mpesa Xpress payment gateway for Woocommerce plugin
Version: 1.0.0
Author: DigitalMold
Author URI: http://digitalmold.co.ke/
Plugin URI: https://developer.pesapal.com/official-extensions?download=8:woocommerce
*/
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

// Make sure WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) return;

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

define('MPESAROOTDIRECTORY', plugin_dir_path(__FILE__));
require_once(MPESAROOTDIRECTORY . 'sql/install_sql.php');
register_activation_hook(__FILE__, 'activate_mpesa');

/**
 * Mpesa Xpress Payment Gateway
 *
 * Provides an Mpesa Payment Gateway;
 *
 * @class       WC_Mpesa_Xpress_Gateway
 * @extends     WC_Payment_Gateway
 * @version     1.0.0
 * @package     WooCommerce/Classes/Payment
 */

add_action('plugins_loaded', 'wc_mpesa_xpress_gateway_init', 11);
function wc_mpesa_xpress_gateway_init()
{

    class WC_Mpesa_Xpress_Gateway extends WC_Payment_Gateway
    {

        public function __construct()
        {
            global $wpdb;
            $this->id = 'mpesa_xpress';
            $this->title = $this->get_option('title');
            $this->icon = "";
            $this->method_title = 'Mpesa Xpress';
            $this->has_fields = false;
            $this->method_description = 'Enable payment via Mpesa STK Push';
            $this->domain = 'wc_mpesa_xpress';
            $this->table = $wpdb->prefix . "mpesa_payment";

            $this->init_form_fields();
            $this->init_settings();

            $this->description = $this->get_option('description');
            $this->instructions = $this->get_option('instructions');
            $this->enabled = $this->get_option('enabled');
            $this->testmode = 'yes' === $this->get_option('testmode');

//            add_action('wp_enqueue_scripts', [$this, 'payment_scripts']);
            add_filter('woocommerce_thankyou_order_received_text', [$this, 'thankYouMessage'],10,2);

            add_action('wp_ajax_mpesa_request', [$this, 'mpesaRequest']);
            add_action('wp_ajax_nopriv_mpesa_request', [$this, 'mpesaRequest']);

            add_action('woocommerce_api_mpesa_call_back', [$this, 'call_back']);

            // This action hook saves the settings
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }

        /**
         * Initialize Gateway Settings Form Fields
         */
        public function init_form_fields()
        {

            $domain = $this->domain;
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', $domain),
                    'type' => 'checkbox',
                    'label' => __('Enable Mpesa XPress Payment', $domain),
                    'default' => 'no'
                ),
                'title' => array(
                    'title' => __('Title', $domain),
                    'type' => 'text',
                    'description' => __('This controls the title which the user sees during checkout.', $domain),
                    'default' => 'Mpesa XPress Payment',
                    'desc_tip' => true,
                ),
                'conversion_rate' => array(
                    'title' => __( 'Conversion rate', 'woothemes' ),
                    'type' => 'text',
                    'description' => __( 'Conversion rate from selected currency to KSH. e.g. 108 for USD i.e $1 = KSH. 108', 'woothemes' ),
                    'default' => __( '108', 'woothemes' )
                ),

                'description' => array(
                    'title' => __('Description', $domain),
                    'type' => 'textarea',
                    'description' => __('Payment method description that the customer will see on your checkout.', $domain),
                    'default' => __('Payment via Mpesa STK Push.', $domain),
                    'desc_tip' => true,
                ),
                'instructions' => array(
                    'title' => __('Thank you page instructions', $domain),
                    'type' => 'textarea',
                    'description' => __('Instructions that the customer will see on thank you page.', $domain),
                    'default' => __('Enter your M-Pesa pin on your phone when prompted to complete the payment', $domain),
                    'desc_tip' => true,
                ),
                'testmode' => array(
                    'title' => __('Test mode', $domain),
                    'label' => __('Enable Test Mode', $domain),
                    'type' => 'checkbox',
                    'description' => __('Place the payment gateway in test mode.', $domain),
                    'default' => 'yes',
                    'desc_tip' => true,
                ),

                'business_shortCode' => array(
                    'title' => __('Mpesa Business Shortcode', $domain),
                    'label' => __('Mpesa Business Shortcode', $domain),
                    'type' => 'text',
                    'description' => __('Mpesa Business Shortcode', $domain),
                    'default' => '',
                    'desc_tip' => true,
                ),

                'consumer_key' => array(
                    'title' => __('Mpesa Consumer Key', $domain),
                    'label' => __('Mpesa Consumer Key', $domain),
                    'type' => 'text',
                    'description' => __('Mpesa Consumer Key', $domain),
                    'default' => '',
                    'desc_tip' => true,
                ),

                'consumer_secret' => array(
                    'title' => __('Mpesa Consumer Secret', $domain),
                    'label' => __('Mpesa Consumer Secret', $domain),
                    'type' => 'text',
                    'description' => __('Mpesa Consumer Secret', $domain),
                    'default' => '',
                    'desc_tip' => true,
                ),
                'pass_key' => array(
                    'title' => __('Mpesa Pass Key', $domain),
                    'label' => __('Mpesa Pass Key', $domain),
                    'type' => 'text',
                    'description' => __('Mpesa Pass Key', $domain),
                    'default' => '',
                    'desc_tip' => true,
                ),

                'account_reference' => array(
                    'title' => __('Mpesa Account Reference', $domain),
                    'label' => __('Mpesa Account Reference', $domain),
                    'type' => 'text',
                    'description' => __('Mpesa Account Reference', $domain),
                    'default' => get_bloginfo('name'),
                    'desc_tip' => true,
                ),

                'transaction_desc' => array(
                    'title' => __('Mpesa Transaction Description', $domain),
                    'label' => __('Mpesa Transaction Description', $domain),
                    'type' => 'text',
                    'description' => __('Mpesa Transaction Description', $domain),
                    'default' => get_bloginfo('name'),
                    'desc_tip' => true,
                ),
            );
        }

        public function mpesaRequest($order)
        {
            $passKey = $this->get_option('pass_key');
            $consumerKey = $this->get_option('consumer_key');
            $consumerSecret = $this->get_option('consumer_secret');
            $AccountRefernce = $this->get_option('account_reference');
            $transactionDesc = $this->get_option('transaction_desc');
            $businessCode = $this->get_option('business_shortCode');
            $conversion_rate = $this->get_option('conversion_rate');
            $testmode = $this->get_option('testmode');

            $callBack = site_url("index.php/wc-api/mpesa_call_back", "https");

            try {
                $mpesaHelper = new MpesaHelper($passKey, $consumerKey, $consumerSecret, $AccountRefernce, $transactionDesc, $businessCode, $testmode);

                return $mpesaHelper->stkRequest($order, $callBack, $conversion_rate);
            } catch (Exception $exception) {
                return $exception->getMessage();
            }
        }

        public function call_back()
        {
            update_option('webhook_debug', $_GET);

            $this->process_callback();
        }

        public function process_callback()
        {
            $mpesaResponse = new stkPushResponse();

            $stkCallbackResponse = $mpesaResponse->stkPushResponseData();

            if ($stkCallbackResponse) {
                $response = json_decode($stkCallbackResponse);

                $body = $response->Body;
                $stkCallback = $body->stkCallback;
                $MerchantRequestID = $stkCallback->MerchantRequestID;
                $CheckoutRequestID = $stkCallback->CheckoutRequestID;
                $ResultCode = (int)$stkCallback->ResultCode;
                $ResultDesc = $stkCallback->ResultDesc;

                global $wpdb;
                $table = $this->table;
                $sql = "SELECT t.* from $table t WHERE t.MerchantRequestId like '$MerchantRequestID'";
                $qr = $wpdb->get_results($sql);


                if ($qr) {
                    $id = $qr[0]->id;
                    $orderId = $qr[0]->order_id;
                    $amount = $qr[0]->amount;
                    $status = $qr[0]->status;
                    $transaction_date = $qr[0]->transaction_date;
                    $order = wc_get_order($orderId);


                    if ($ResultCode == 0) {
                        $CallbackMetadata = $stkCallback->CallbackMetadata;
                        $Item = $CallbackMetadata->Item;
                        if (is_array($Item)) {
                            $Amount = $Item[0]->Value;
                            $MpesaReceiptNumberValue = $Item[1]->Value;

                            //Update order status

                            $order->payment_complete();
                            $order->reduce_order_stock();

                            //update mpesa_payment
                            $sql = "UPDATE $table t SET t.status = 'PAID', t.MpesaReceiptNumber='$MpesaReceiptNumberValue', t.ResultCode='$ResultCode', t.ResultDesc='$ResultDesc'  WHERE t.id = '$id'";
                            $wpdb->query($sql);

                            $message = "You have successfully completed payment for order " . $order->get_id . " for the amount " . $order->get_currency() . " " . $amount;
                            $message .= PHP_EOL . ". Your Mpesa transaction ID is " . $MpesaReceiptNumberValue . ". Your order is now fully paid.";
                            $message .= PHP_EOL . "Kind regards,";
                            $message .= PHP_EOL . ucfirst(get_bloginfo('name'));
                            $order->add_order_note(  ".Mpesa transaction ID is " . $MpesaReceiptNumberValue );
                            

                        }
                    } else {
                        //
                        $order->add_order_note( $ResultDesc);

                    }

                } else {
                    //handle no result
                }
            }
        }

        //override thank you message
        public function thankYouMessage($str, $order)
        {
            /**@var $order WC_Order **/
            if (!$order->is_paid()){
                $str = $this->instructions;
                $str .= "<br>Click <a href=''>here</a> to refresh the page after you've completed your payment.";
            }
            return $str;
        }

// Process the Payment
        public function process_payment($order_id)
        {

            global $woocommerce;

// we need it to get any order detailes
            $order = wc_get_order($order_id);

// Mark as on-hold (we're awaiting the payment)
            $order->update_status('on-hold', __('Awaiting mpesa payment', $this->domain));

            //Mpesa Push
            $message = $this->mpesaRequest($order);




            // Reduce stock levels
//            $order->reduce_order_stock();

            // Remove cart
            WC()->cart->empty_cart();

            // Return thankyou redirect
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url($order)
            );
        }

        public function payment_scripts()
        {

            if (!is_cart() && !is_checkout() && !isset($_GET['pay_for_order'])) {
                return;
            }

            if ('no' === $this->enabled) {
                return;
            }

            /*wp_enqueue_script('woocommerce_mpesa_xpress-js', plugins_url('/index.js', __FILE__), array('jquery'));
            wp_localize_script('woocommerce_mpesa_xpress-js', 'plugin_ajax_object',
                array('ajax_url' => admin_url('admin-ajax.php')));*/

        }

        /**
         * Output for the order received page.
         */
        public function thankyou_page()
        {
            if ($this->instructions) {
                echo wpautop(wptexturize($this->instructions));
            }
        }


        /**
         * Add content to the WC emails.
         *
         * @access public
         * @param WC_Order $order
         * @param bool $sent_to_admin
         * @param bool $plain_text
         */
        public function email_instructions($order, $sent_to_admin, $plain_text = false)
        {

            if ($this->instructions && !$sent_to_admin && 'offline' === $order->payment_method && $order->has_status('pending')) {
                echo wpautop(wptexturize($this->instructions)) . PHP_EOL;
            }
        }

    } // end \WC_Mpesa_Xpress_Gateway class

}


function wc_mpesa_express_add_to_gateways($gateways)
{
    $gateways[] = 'WC_Mpesa_Xpress_Gateway';
    return $gateways;
}

add_filter('woocommerce_payment_gateways', 'wc_mpesa_express_add_to_gateways');

require_once(MPESAROOTDIRECTORY . 'lib/MpesaHelper.php');
require_once(MPESAROOTDIRECTORY . 'lib/MpesaConfigExt.php');