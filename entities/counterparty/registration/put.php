<?php
namespace dreamwhiteAPIv1;
header("Access-Control-Allow-Origin: *");
require "../../../includes.php";

$data = json_decode(file_get_contents('php://input'), true)['data'];

$counterparty = DataHelper::encode($data, 'anketa-sms');

$manager = new CounterpartyManager();
$manager->put($counterparty);

if ($counterparty->email() !== '') {
    $userManager = new WPUserManager();
    $userManager->createUser($counterparty);
}


echo $manager->encode($counterparty);