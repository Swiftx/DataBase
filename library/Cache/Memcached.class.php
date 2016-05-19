<?php
namespace Swiftx\DataBase\Cache;
/**
 * ---------------------------------------------------------------------------------------------------------------
 * 缓存驱动器
 * ---------------------------------------------------------------------------------------------------------------
 * @author      胡永强 <odaytudio@gmail.com>
 * @since       2014-11-06
 * @copyright   Copyright (c) 2014-2015 ymaplus Inc.
 * ---------------------------------------------------------------------------------------------------------------
 */
class Memcached implements \ArrayAccess{

    private $_driver;
    private $_overtime;
    private $_prefix;
    private $_tempData;

    public function __construct($overtime, $prefix){
        $this->_overtime = $overtime;
        $this->_prefix = $prefix;
        $this->_driver = new \memcached();
    }

    /**
     * 绑定服务器
     * @return void
     */
    public function BindServer($address){
        $temp = explode(':', $address);
        $this->_driver->addServer($temp[0], $temp[1]);
        return $this;
    }

    public function offsetExists($offset){
        return !($this->_driver->get($this->_prefix.bin2hex($offset)) === false);
    }

    public function offsetGet($offset){
        return $this->_driver->get($this->_prefix.bin2hex($offset));
    }

    public function offsetSet($offset, $value){
        $this->_driver->set($this->_prefix.bin2hex($offset), $value, \time() + $this->_overtime);
        return $value;
    }

    public function offsetUnset($offset){
        return $this->_driver->delete($this->_prefix.bin2hex($offset));
    }
}