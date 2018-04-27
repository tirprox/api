<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 3/30/18
 * Time: 1:03 PM
 */

namespace dreamwhiteAPIv1;


class Log
{

    static public function log(string $message) : void {
        file_put_contents(dirname(__DIR__) . '/logs/log.txt',  date('Y-m-d H:i:s') . ": " . $message.PHP_EOL , FILE_APPEND | LOCK_EX);
    }


}