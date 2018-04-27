<?php
namespace dreamwhiteAPIv1;

require "../../includes.php";

function getFiles() {
    $manager = new CounterpartyManager();
    $time_pre = microtime(true);
    $counterparties = $manager->getAll();
    $reports = $manager->getAllReports();

    $partyMap = [];
    $reportMap = [];

    foreach ($counterparties as $party) {
        unset(
            $party['meta'],
            $party['version'],
            $party['archived'],
            $party['externalCode'],
            $party['companyType'],
            $party['notes'],
            $party['state'],
            $party['contactpersons'],
            $party['accounts'],
            $party['shared'],
            $party['accountId']
        );

        $attrs = [];
        foreach ($party['attributes'] as $attr) {
            $attrs[$attr['name']] = $attr['value'];
        }

        $party['attributes'] = $attrs;

        $party['owner'] = $party['owner']['meta']['href'];
        $party['group'] = $party['group']['meta']['href'];

        $partyMap[$party['id']] = $party;

    }

    foreach ($reports as $report) {
        if ($report['counterparty']['companyType'] === 'individual') {
            $reportMap[$report['counterparty']['id']] = $report;
            unset (
                $reportMap[$report['counterparty']['id']]['counterparty'],
                $reportMap[$report['counterparty']['id']]['meta']
            );
        }
    }

    $map = [];
    foreach ($reportMap as $id => $report) {
        $map[] = array_merge($report, $partyMap[$id]);

    }

    $time_post = microtime(true);
    $exec_time = $time_post - $time_pre;
    echo $exec_time . "\n";

    file_put_contents("map.json", json_encode($map, JSON_UNESCAPED_UNICODE));

}

//getFiles();
$visitors = new VisitorManager();
$visitors->buckets();