<?php
//require "REGExporter.php";
$data = json_decode(file_get_contents('php://input'));

$exporter = new REGExporter();

$exporter->exportCounterpartyFromAnketaJSON($data);
$exporter->completeAllRequests();
