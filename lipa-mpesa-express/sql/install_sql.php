<?php

function activate_mpesa(){

    global $wpdb;
    $table = $wpdb->prefix . "mpesa_payment";
    $sql = "
            CREATE TABLE IF NOT EXISTS `$table`(
            `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `order_id` VARCHAR(30) NOT NULL,
            `MerchantRequestID` VARCHAR(50) NOT NULL UNIQUE,
            `CheckoutRequestID` VARCHAR(50),
            `ResponseDescription` VARCHAR(50),
            `CustomerMessage` VARCHAR(225),
            `ResultCode` VARCHAR(225),
            `ResultDesc` VARCHAR(225),
            `MpesaReceiptNumber` VARCHAR(50) UNIQUE,
            `amount` VARCHAR(10),
            `status` VARCHAR(10),
            `transaction_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            );
            
        ";
    dbDelta($sql);
}