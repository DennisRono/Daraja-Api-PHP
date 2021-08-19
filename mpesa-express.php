<!--
    /** Mpesa Express Daraja Api **/
    /** https://denniskibet.com **/
    /** Dennis Kibet **/
-->


<?php

    //get server base name
    $server = (isset($_SERVER["HTTPS"]) ? "https" : "http") . "://" . (isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : '');

    /* global details */
    $key = "a5YkD1vMqhRvZ9yDlbwlW0PCTbk08fBk";
    $secret = "IP5STQzreXu4SGBQ";
    $shortcode = "174379";
    $timestamp = date("YmdHis");
    $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
    $password = base64_encode($shortcode.$passkey.$timestamp); // shortcode + passkey + timestamp;
    $phone = "254798116710";
    $ammount = 100;
    $callback_url = $server."/api/callback/index.php"; // Where a response is to be sent when a transaction times out
    $reference = "thenullsoft";
    $description = "Transaction Description";
    $remark = "Remark";

    //** sanitize phone number to acceptable format **//
    $phone = (substr($phone, 0, 1) == "+") ? str_replace("+", "", $phone) : $phone;
    $phone = (substr($phone, 0, 1) == "0") ? preg_replace("/^0/", "254", $phone) : $phone;
    $phone = (substr($phone, 0, 1) == "7") ? "254{$phone}" : $phone;

    //** get access token **//
    $headers = ['Content-Type:application/json; charset=utf8'];
    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $curl = curl_init($url);
    
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_HEADER, FALSE);
    curl_setopt($curl, CURLOPT_USERPWD, $key.':'.$secret);
    $result = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $result = json_decode($result);
    $access_token = $result->access_token;    
    curl_close($curl);

    //** register your urls **//
    $url = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl';
    $confirmationUrl = $server. '/api/confirm/index.php';
    $validationUrl = $server. '/api/validate/index.php';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$access_token));
    $curl_post_data = array(
        'ShortCode' => $shortcode,
        'ResponseType' => 'Confirmed',
        'ConfirmationURL' => $confirmationUrl,
        'ValidationURL' => $validationUrl
    );
    $data_string = json_encode($curl_post_data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    $curl_response = curl_exec($curl);

    //** mpesa xpress **//
    $ch = curl_init('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer '.$access_token,
        'Content-Type: application/json'
    ]);

    $post_data = array(
        "BusinessShortCode" => $shortcode,
        "Password" => $password,
        "Timestamp" => $timestamp,
        "TransactionType" => "CustomerPayBillOnline",
        "Amount" => $ammount,
        "PartyA" => $phone,
        "PartyB" => $shortcode,
        "PhoneNumber" => $phone,
        "CallBackURL" => $callback_url,
        "AccountReference" => "thenullsoft",
        "TransactionDesc" => "Activate account"
    );
    $data = json_encode($post_data);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response     = curl_exec($ch);
    curl_close($ch);
?>