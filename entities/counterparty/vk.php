<?php
namespace dreamwhiteAPIv1;

require_once "../../includes.php";

$oauth = new \VK\OAuth\VKOAuth();
$client_id = 	6469016;
$redirect_uri = 'https://dreamwhite.ru';
$display = \VK\OAuth\VKOAuthDisplay::PAGE;
$scope = array(\VK\OAuth\Scopes\VKOAuthUserScope::WALL, \VK\OAuth\Scopes\VKOAuthUserScope::GROUPS);
$state = 'secret_state_code';

$browser_url = $oauth->getAuthorizeUrl(\VK\OAuth\VKOAuthResponseType::CODE, $client_id, $redirect_uri, $display, $scope, $state);
var_dump($browser_url);