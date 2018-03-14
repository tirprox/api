<?php
namespace dreamwhiteAPIv1;

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

require "vendor/autoload.php";

require_once (dirname(__DIR__, 2) . '/wp-load.php');

require "entities/counterparty/Counterparty.php";
require "entities/counterparty/CounterpartyManager.php";
require "entities/counterparty/registration/DataHelper.php";

require "tools/Auth.php";
require "tools/Connector.php";
require "tools/SendSMS.php";
require "tools/sms.ru.php";
require "tools/Tools.php";
require "tools/WPUserManager.php";




