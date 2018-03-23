<?php
/**
 * Created by PhpStorm.
 * User: dreamwhite
 * Date: 16.03.2018
 * Time: 18:11
 */

namespace dreamwhiteAPIv1;


class Input
{
    static function get() {
        return json_decode(file_get_contents('php://input'), true);
    }
}