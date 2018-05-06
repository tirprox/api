<?php
namespace dreamwhiteAPIv1;

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

require_once "vendor/autoload.php";

include_once (dirname(__DIR__, 2) . '/wp-load.php');

require_once "entities/counterparty/Counterparty.php";
require_once "entities/counterparty/CounterpartyManager.php";
require_once "entities/counterparty/VisitorManager.php";
require_once "entities/counterparty/registration/DataHelper.php";

require_once "entities/assortment/Assortment.php";

require_once "tools/Auth.php";
require_once "tools/Connector.php";
require_once "tools/Input.php";
require_once "tools/Log.php";
require_once "tools/SendSMS.php";
require_once "tools/sms.ru.php";
require_once "tools/Tools.php";
require_once "tools/WPUserManager.php";




