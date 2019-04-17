<?php
namespace dreamwhiteAPIv1;
require "../../../includes.php";

$input = file_get_contents('spb.json');

$data = json_decode($input, true);

$sms = new SendSMS();
foreach ($data["RECORDS"] as $person) {
    //echo($person["phone"]);
    $sms->sendBySite($person['phone'], $person['name'], 'dreamwhite.ru');
}