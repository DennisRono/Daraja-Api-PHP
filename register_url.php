<?php
    $ch = curl_init('https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer '.$access_token,
        'Content-Type: application/json'
    ]);
    $the_data = array(
        "ShortCode" => 174379,
        "ResponseType" => "Completed",
        "ConfirmationURL" => "https://thenullsoft.co.ke/api/confirm/index.php",
        "ValidationURL" => "https://thenullsoft.co.ke/api/validate/index.php"
    );
    $data = json_encode($the_data);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response     = curl_exec($ch);
    curl_close($ch);
    echo $response;
?>