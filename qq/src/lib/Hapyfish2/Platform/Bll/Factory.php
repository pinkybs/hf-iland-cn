<?php

class Hapyfish2_Platform_Bll_Factory
{
    /* user */
    public static function getUser($uid)
    {
        if (defined('PLATFORM_SOURCE') && '1' == PLATFORM_SOURCE) {
            $result = Hapyfish2_Platform_Bll_UserQzone::getUser($uid);
        }
        else {
            $result = Hapyfish2_Platform_Bll_User::getUser($uid);
        }
        return $result;
    }

    public static function getMultiUser($fids)
    {
        if (defined('PLATFORM_SOURCE') && '1' == PLATFORM_SOURCE) {
            $result = Hapyfish2_Platform_Bll_UserQzone::getMultiUser($fids);
        }
        else {
            $result = Hapyfish2_Platform_Bll_User::getMultiUser($fids);
        }
        return $result;
    }

    public static function addUser($user)
    {
        if (defined('PLATFORM_SOURCE') && '1' == PLATFORM_SOURCE) {
            $result = Hapyfish2_Platform_Bll_UserQzone::addUser($user);
        }
        else {
            $result = Hapyfish2_Platform_Bll_User::addUser($user);
        }
        return $result;
    }

    public static function updateUser($uid, $user, $savedb = false)
    {
        if (defined('PLATFORM_SOURCE') && '1' == PLATFORM_SOURCE) {
            $result = Hapyfish2_Platform_Bll_UserQzone::updateUser($uid, $user, $savedb);
        }
        else {
            $result = Hapyfish2_Platform_Bll_User::updateUser($uid, $user, $savedb);
        }
        return $result;
    }

    public static function getUids($pids)
    {
        if (defined('PLATFORM_SOURCE') && '1' == PLATFORM_SOURCE) {
            $result = Hapyfish2_Platform_Bll_UserQzone::getUids($pids);
        }
        else {
            $result = Hapyfish2_Platform_Bll_User::getUids($pids);
        }
        return $result;
    }


    /* friend */
    public static function getFriend($uid)
    {
        if (defined('PLATFORM_SOURCE') && '1' == PLATFORM_SOURCE) {
            $result = Hapyfish2_Platform_Bll_FriendQzone::getFriend($uid);
        }
        else {
            $result = Hapyfish2_Platform_Bll_Friend::getFriend($uid);
        }
        return $result;
    }

    public static function updateFriend($uid, $fids, $highcache = false)
    {
        if (defined('PLATFORM_SOURCE') && '1' == PLATFORM_SOURCE) {
            $result = Hapyfish2_Platform_Bll_FriendQzone::updateFriend($uid, $fids, $highcache);
        }
        else {
            $result = Hapyfish2_Platform_Bll_Friend::updateFriend($uid, $fids, $highcache);
        }
        return $result;
    }

    public static function addFriend($uid, $fids, $highcache = false)
    {
        if (defined('PLATFORM_SOURCE') && '1' == PLATFORM_SOURCE) {
            $result = Hapyfish2_Platform_Bll_FriendQzone::addFriend($uid, $fids, $highcache);
        }
        else {
            $result = Hapyfish2_Platform_Bll_Friend::addFriend($uid, $fids, $highcache);
        }
        return $result;
    }

    public static function getFriendIds($uid)
    {
        if (defined('PLATFORM_SOURCE') && '1' == PLATFORM_SOURCE) {
            $result = Hapyfish2_Platform_Bll_FriendQzone::getFriendIds($uid);
        }
        else {
            $result = Hapyfish2_Platform_Bll_Friend::getFriendIds($uid);
        }
        return $result;
    }

    public static function getFriendCount($uid)
    {
        if (defined('PLATFORM_SOURCE') && '1' == PLATFORM_SOURCE) {
            $result = Hapyfish2_Platform_Bll_FriendQzone::getFriendCount($uid);
        }
        else {
            $result = Hapyfish2_Platform_Bll_Friend::getFriendCount($uid);
        }
        return $result;
    }

    public static function isFriend($uid, $fid)
    {
        if (defined('PLATFORM_SOURCE') && '1' == PLATFORM_SOURCE) {
            $result = Hapyfish2_Platform_Bll_FriendQzone::isFriend($uid, $fid);
        }
        else {
            $result = Hapyfish2_Platform_Bll_Friend::isFriend($uid, $fid);
        }
        return $result;
    }


    /* cache */
    public static function getStatus($uid)
    {
        if (defined('PLATFORM_SOURCE') && '1' == PLATFORM_SOURCE) {
            $result = Hapyfish2_Platform_Cache_UserQzone::getStatus($uid);
        }
        else {
            $result = Hapyfish2_Platform_Cache_User::getStatus($uid);
        }
        return $result;
    }

    public static function updateStatus($uid, $status, $savedb = true)
    {
        if (defined('PLATFORM_SOURCE') && '1' == PLATFORM_SOURCE) {
            $result = Hapyfish2_Platform_Cache_UserQzone::updateStatus($uid, $status, $savedb);
        }
        else {
            $result = Hapyfish2_Platform_Cache_User::updateStatus($uid, $status, $savedb);
        }
        return $result;
    }

}