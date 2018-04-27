<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 3/30/18
 * Time: 1:50 PM
 */
namespace dreamwhiteAPIv1;
header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');

require "../../../includes.php";

$manager = new CounterpartyManager();
$counterparty = $manager->getByPhone('9817001024');


print($manager->encode($counterparty));