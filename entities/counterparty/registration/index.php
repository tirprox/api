<?php
namespace dreamwhiteAPIv1;

require_once "../includes.php";

$method = $_SERVER['REQUEST_METHOD'];


switch($method) {
    case 'POST': post(); break;
    case 'GET': post(); break;
    case 'PUT': put(); break;
}



function getInput() {
    return file_get_contents("php://input");
}

function get() {

    echo 'get';
}

function post() {
    echo 'post';
}

function put() {
    echo 'put';
}