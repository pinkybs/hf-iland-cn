<?php

class Qzone_Factory
{
    public static function getRest()
    {
        if (defined('PLATFORM_SOURCE') && '1' == PLATFORM_SOURCE) {
            $rest = Qzone_RestQzone::getInstance();
        }
        else {
            $rest = Qzone_Rest::getInstance();
        }
        return $rest;
    }

}