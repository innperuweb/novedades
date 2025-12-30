<?php
// Identificador de su tienda
define("USERNAME", "83560494");

// Clave de Test o Producción
define("PASSWORD", "testpassword_0yi5K5xk2suMKiT0bfWadQrC0Xi6ZiWWcHvSlUjdk7xjG");

// Clave Pública de Test o Producción
define("PUBLIC_KEY","83560494:testpublickey_7M8TA2wQx0mARQ66gcmpei2qU1alDKxob5V4Bp5AVz5gJ");

// Clave HMAC-SHA-256 de Test o Producción
define("HMAC_SHA256","xZVBhtl86V0w2deTaDSxWC4xUO600hAt2oWXBQpI9IxzD");

function formToken(){
    // URL de Web Service REST
    $url = "https://api.micuentaweb.pe/api-payment/V4/Charge/CreatePayment";

    // Encabezado Basic con concatenación de "usuario:contraseña" en base64
    $auth = USERNAME.":".PASSWORD;

    $headers = array(
        "Authorization: Basic " . base64_encode($auth),
        "Content-Type: application/json"
    );

    $body = [
        "amount" => $_POST["amount"] * 100,
        "currency" => $_POST["currency"],
        "orderId" => $_POST["orderId"],
        "customer" => [
          "email" => $_POST["shipping_email"],
          "billingDetails" => [
            "firstName"=>  $_POST["billing_fname"],
            "lastName"=>  $_POST["billing_lname"],
            "identityCode"=>  $_POST["billing_dni"],
            "phoneNumber"=>  $_POST["billing_phone"],

            "identityType"=>  $_POST["identityType"],            
            "address"=>  $_POST["address"],
            "country"=>  $_POST["country"],
            "city"=>  $_POST["city"],
            "state"=>  $_POST["state"],
            "zipCode"=>  $_POST["zipCode"],
          ]
        ],
    ];

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $raw_response = curl_exec($curl);

    $response = json_decode($raw_response , true);

    $formToken = $response["answer"]["formToken"];

    return $formToken;
}

function checkHash($key){
    $krAnswer = str_replace('\/', '/',  $_POST["kr-answer"]);

    $calculateHash = hash_hmac("sha256", $krAnswer, $key);

    return ($calculateHash == $_POST["kr-hash"]) ;
}