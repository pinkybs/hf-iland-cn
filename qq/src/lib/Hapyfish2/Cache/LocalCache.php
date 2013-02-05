<?php

class Hapyfish2_Cache_LocalCache 
{
    protected static $_instance;
    
    protected $_cache = null;
    
    /**
     * Single Instance
     *
     * @return Hapyfish2_Cache_LocalCache
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
	public function __construct()
	{
		$this->_cache = array();
	}
    
    public function set($key, $data, $varcache = true, $ttl = 0)
    {
        apc_store($key, $data, $ttl);
        if ($varcache) {
    		$this->_cache[$key] = $data;
        }
    }

    public function delete($key, $varcache = true)
    {
        apc_delete($key);
        if ($varcache) {
    		unset($this->_cache[$key]);
        }
    }
    
    public function get($key, $varcache = true)
    {
    	if ($varcache && isset($this->_cache[$key])) {
    		return $this->_cache[$key];
    	}
    	
    	$data = apc_fetch($key);
    	$this->_cache[$key] = $data;
    	
    	return $data;
    }
    
    public function flush()
    {
    	$this->_cache = array();
    	apc_clear_cache('user');
    }
}