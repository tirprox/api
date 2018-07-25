<?php
namespace dreamwhiteAPIv1;
require "../includes.php";


$user = "admin";
$pwd = '6h8s4ksoq';

$client = new \MongoDB\Client("mongodb://${user}:${pwd}@localhost:27017");
$manager = new CounterpartyManager();

$sklad = \MoySklad\MoySklad::getInstance(Auth::login, Auth::password);



class Backup {
 const options = ['upsert' => true];
}


$options = ['upsert' => true];


backupCounterparties($manager, $client);
backupCounterpartyreports($manager, $client);


//backupProducts($sklad, $client);

//backupVariants($sklad, $client);
//backupAssortment($sklad, $client);


function backupAssortment($sklad, $client) {
    $assortmentCollection = $client->msmirror->assortment;
    try {
        $products = \MoySklad\Entities\Assortment::query($sklad, \MoySklad\Components\Specs\QuerySpecs\QuerySpecs::create([
            "limit" => 100, "maxResults" => "100"
        ]))->getList()->toArray();
    } catch (\Exception $e) {

    }

    //$products = $products->toArray();

    foreach ($products as $product) {
        $product = $product->jsonSerialize();
        $filter  = ['id' => $product['id']];
        $assortmentCollection->updateOne($filter, ['$set' => $product], Backup::options);
    }

}





function backupProducts($sklad, $client) {
    $productCollection = $client->msmirror->products;
    try {
        $products = \MoySklad\Entities\Products\Product::query($sklad, \MoySklad\Components\Specs\QuerySpecs\QuerySpecs::create([
            "limit" => 100,
        ]))->getList()->toArray();
    } catch (\Exception $e) {

    }

    //$products = $products->toArray();

    foreach ($products as $product) {
        $item = $product->jsonSerialize();

        $filter  = ['id' => $item->id];
        $productCollection->updateOne($filter, ['$set' => $item], Backup::options);
    }

}


function backupVariants($sklad, $client) {
    $productCollection = $client->msmirror->variants;
    try {
        $products = \MoySklad\Entities\Products\Variant::query($sklad,
            \MoySklad\Components\Specs\QuerySpecs\QuerySpecs::create(["limit" => 100, "maxResults" => 100]))->withExpand(\MoySklad\Components\Expand::create(['product']))->getList()->toArray();
    } catch (\Exception $e) {
    }

    //$products = $products->toArray();

    foreach ($products as $product) {
        $item = $product->jsonSerialize();;

        var_dump($product);

        $filter  = ['id' => $item->id];
        $productCollection->updateOne($filter, ['$set' => $item], Backup::options);
    }

}


function backupCounterparties($manager, $client) {

    $counterparties = $manager->getAll();
    $counterpartyCollection = $client->msmirror->counterparties;

    foreach ($counterparties as $counterparty) {
        $filter  = ['id' => $counterparty['id']];
        $counterpartyCollection->updateOne($filter, ['$set' => $counterparty], Backup::options);
    }
}

function backupCounterpartyreports ($manager, $client) {
    //$manager = new CounterpartyManager();
    $counterpartyReportCollection = $client->msmirror->counterpartyReports;

    $reports = $manager->getAllReports();

    foreach ($reports as $report) {

        $filter  = ['counterparty.id' => $report['counterparty']['id']];
        $counterpartyReportCollection->updateOne($filter, ['$set' => $report], Backup::options);
    }
}