<?php
require 'newsletter.config.php';

$email = $_POST['email'];
if(empty($email)) {
    $email = $_GET['email'];
}
$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
if (filter_var($emailB, FILTER_VALIDATE_EMAIL) === false || $emailB != $email) {
    header('Content-Type: application/json');
    echo json_encode(418);
    exit();
}

$list_id = $conf['list_id'];
$api_key = $conf['api_key'];

$data_center = substr($api_key,strpos($api_key,'-')+1);

$url = 'https://'. $data_center .'.api.mailchimp.com/3.0/lists/'. $list_id .'/members';

$json = json_encode([
    'email_address' => $email,
    'status'        => 'pending', //pass 'subscribed' or 'pending'
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $api_key);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
$result = curl_exec($ch);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
header('Content-Type: application/json');
echo json_encode($status_code);

