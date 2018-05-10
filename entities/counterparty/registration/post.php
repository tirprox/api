<?php

namespace dreamwhiteAPIv1;
header("Access-Control-Allow-Origin: *");
require "../../../includes.php";
$data = json_decode(file_get_contents('php://input'), true);
if (array_key_exists('data', $data)) {
    $data = $data['data'];
}

$manager = new CounterpartyManager();
$retrieved = $manager->getByPhone($data['client']['phone']['full']);

$exists = $retrieved->id() !== '';

if (!$exists) {
    $retrieved = $manager->getByEmail($data['client']['email']);
    $exists = $retrieved->id() !== '';
}


$counterparty = DataHelper::encode($data, 'anketa-site');




//Log::log($counterparty->id() . ' ' . $retrieved->id() . ' ' . $exists ? 'found' : 'not found' . '\n');
//Log::log($manager->encode($retrieved) . '\n');

if (!$exists)  {
    $manager->post($counterparty);
}

else {
    $counterparty->id($retrieved->id());
    $manager->put($counterparty);
}

//$manager->post($counterparty);

$userManager = new WPUserManager();
$userManager->createUser($counterparty);