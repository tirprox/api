<?php
namespace dreamwhiteAPIv1;
require "../includes.php";


$user = "admin";
$pwd = '6h8s4ksoq';

$client = new \MongoDB\Client("mongodb://${user}:${pwd}@localhost:27017");
$manager = new CounterpartyManager();



class Backup {
 const options = ['upsert' => true];
}


$options = ['upsert' => true];


backupCounterparties($manager, $client);
backupCounterpartyreports($manager, $client);



function backupCounterparties($manager, $client) {

    $counterparties = $manager->getAll();
    $counterpartyCollection = $client->msmirror->counterparties;

    foreach ($counterparties as $counterparty) {
        $filter  = ['id' => $counterparty['id']];
        $counterpartyCollection->updateOne($filter, ['$set' => $counterparty], Backup::options);
    }
}

function backupCounterpartyreports ($manager, $client) {
    $manager = new CounterpartyManager();
    $counterpartyReportCollection = $client->msmirror->counterpartyReports;

    $reports = $manager->getAllReports();

    foreach ($reports as $report) {

        $filter  = ['counterparty.id' => $report['counterparty']['id']];
        $counterpartyReportCollection->updateOne($filter, ['$set' => $report], Backup::options);
    }
}