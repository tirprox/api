<?php

namespace dreamwhiteAPIv1;

header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');

require "../../../includes.php";

/*ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);*/

//$input = json_decode(file_get_contents('php://input'));
//$phone = $input->phone;

$phone = Input::get()['phone'];

$manager = new CounterpartyManager();
$counterparty = $manager->getByPhone($phone);



//$data = [];
$data['id'] = $counterparty->id();
$data['firstName'] =  $counterparty->name();
$data['lastName'] = $counterparty->lastName();
$data['birthday'] = formatBirthday($counterparty->birthday());

$data['country'] = $counterparty->country();
$data['city'] = $counterparty->city();
$data['address'] = $counterparty->address();
$data['postcode'] = $counterparty->postcode();
$data['promoCode'] = $counterparty->promoCode();
$data['phone'] = formatPhone($counterparty->phone());
$data['email'] = $counterparty->email();

$data['group'] = $counterparty->group();
$data['owner'] = $counterparty->owner();

foreach ($data as $key => $value) {
    if ($value==null) {
        $value = '';
    }
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);

function formatPhone(string $phone) : array {
    $prefix = '+7';
    $codes = [
        '+7', // Россия, Казахстан
        '+375' // Беларусь
    ];

    foreach ($codes as $code) {
        if (strpos($phone, $code) !== false) {
            $prefix = $code;
            $phone = str_replace($code, "", $phone);
            break;
        }
    }

    $phone = str_replace("-", "", $phone);
    $phone = str_replace(" ", "", $phone);

    $data = [];
    $data['prefix'] = $prefix;
    $data['number'] = $phone;

    return $data;
}

function formatBirthday($date) : string {
    if ($date!==null) {
        $parsed = date_parse($date);
        if ($parsed['day'] < 10) $parsed['day'] = 0 . $parsed['day'];
        if ($parsed['month'] < 10) $parsed['month'] = 0 . $parsed['month'];

        return $parsed['day']  . '.' . $parsed['month'] . '.' .$parsed['year'] ;
    }
    else return '';

}