<?php

/**
 * seq payorder for sina
 *
 *
 * @package    Dal
 * @create      2011/07/01    zx
 */
class Hapyfish2_Platform_Dal_SeqPayorder
{

    protected static $_instance;

    /**
     *
     *
     * @return Hapyfish2_Platform_Dal_UidMap
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getDB($uid)
    {
    	$id = strtolower(substr($uid, -1, 1));
    	$key = 'db_' . $id;
    	return Hapyfish2_Db_Factory::getBasicDB($key);
    }

    public function getSequence($uid)
    {
    	$name = strtolower(substr($uid, -1, 1));
    	$sql = "UPDATE seq_payorder SET id=LAST_INSERT_ID(id+10) WHERE `name`=:name";

    	$db = $this->getDB($uid);
    	$wdb = $db['w'];
    	$wdb->query($sql, array('name' => $name));
    	return $wdb->lastInsertId();
    }
}