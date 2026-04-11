<?php

if (!function_exists('getLastLoginIP')) {
    function getLastLoginIP($userId)
    {
        return \App\Helpers\LoginHelper::getLastLoginIP($userId);
    }
}

if (!function_exists('getLastDeviceInfo')) {
    function getLastDeviceInfo($userId)
    {
        return \App\Helpers\LoginHelper::getLastDeviceInfo($userId);
    }
}

if (!function_exists('getLastLoginTime')) {
    function getLastLoginTime($userId)
    {
        return \App\Helpers\LoginHelper::getLastLoginTime($userId);
    }
}
