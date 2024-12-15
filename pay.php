
<?php
/*Install Midtrans PHP Library (https://github.com/Midtrans/midtrans-php)
composer require midtrans/midtrans-php
                              
Alternatively, if you are not using **Composer**, you can download midtrans-php library 
(https://github.com/Midtrans/midtrans-php/archive/master.zip), and then require 
the file manually.   

require_once dirname(__FILE__) . '/pathofproject/Midtrans.php'; */
require_once dirname(__FILE__) . '../../vendor/midtrans/midtrans-php/Midtrans.php';

if (!empty($pelanggan)) {
    $query_user = mysqli_query($Koneksi, "SELECT * FROM user_login WHERE nama = '" . mysqli_real_escape_string($Koneksi, $pelanggan) . "'");
    if ($row_user = mysqli_fetch_assoc($query_user)) {
        $email = $row_user['email'];
        $NoTlp = $row_user['NoTlp'];
    }    
}

//SAMPLE REQUEST START HERE

// Set your Merchant Server Key
\Midtrans\Config::$serverKey = 'SB-Mid-server-p0J5Kw0tX_JHY_HoYJOQzYXQ';
// Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
\Midtrans\Config::$isProduction = false;
// Set sanitization on (default)
\Midtrans\Config::$isSanitized = true;
// Set 3DS transaction for credit card to true
\Midtrans\Config::$is3ds = true;

$params = array(
    'transaction_details' => array(
        'order_id' => $_POST['kode_order'],
        'gross_amount' => $_POST['total'],
    ),
    'item_details' => json_decode($_POST['tems'], true),
    'customer_details' => array(
        'first_name' => $_POST['pelanggan'],
        'email' => $email,
        'phone' => $NoTlp,
    ),
);

$snapToken = \Midtrans\Snap::getSnapToken($params);
// echo $snapToken;
?>