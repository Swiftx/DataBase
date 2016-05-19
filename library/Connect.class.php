<?php
namespace Swiftx\DataBase;
use Swiftx\System\Object;
/**
 * ---------------------------------------------------------------------------------------------------------------------
 * 数据库对象
 * ---------------------------------------------------------------------------------------------------------------------
 * @author      胡永强 <odaytudio@gmail.com>
 * @since       2015-12-08
 * @copyright   Copyright (c) 2014-2015 Swiftx Inc.
 * ---------------------------------------------------------------------------------------------------------------------
 * @property string Host 主机地址
 * @property string Name 数据库名
 * @property string Username    登陆账号
 * @property string Password    登陆密码
 * @property int Port    端口号
 * @property string Code 编码格式
 * @property string Socket 套接字
 * ---------------------------------------------------------------------------------------------------------------------
 */
final class Connect extends Object {

    protected $_host = null;
    protected $_name = null;
    protected $_username = null;
    protected $_password = null;
    protected $_port = 3306;
    protected $_socket = null;
    protected $_code = 'utf8';

    /**
     * 主机地址
     * @return string
     */
    protected function _getHost(){
        return $this->_host;
    }

    /**
     * 设置主机地址
     * @param $value
     */
    protected function _setHost($value){
        $this->_host = $value;
    }

    /**
     * 数据库名
     * @return string
     */
    protected function _getName(){
        return $this->_name;
    }

    /**
     * 设置数据库名
     * @param $value
     */
    protected function _setName($value){
        $this->_name = $value;
    }

    /**
     * 登陆账号
     * @return string
     */
    protected function _getUsername(){
        return $this->_username;
    }

    /**
     * 设置登陆账号
     * @param $value
     */
    protected function _setUsername($value){
        $this->_username = $value;
    }

    /**
     * 登陆密码
     * @return string
     */
    protected function _getPassword(){
        return $this->_password;
    }

    /**
     * 设置登陆密码
     * @param $value
     */
    protected function _setPassword($value){
        $this->_password = $value;
    }

    /**
     * 端口号
     * @return int
     */
    protected function _getPort(){
        return $this->_port;
    }

    /**
     * 设置端口号
     * @param $value
     */
    protected function _setPort($value){
        $this->_port = $value;
    }

    /**
     * 编码格式
     * @return string
     */
    protected function _getCode(){
        return $this->_code;
    }

    /**
     * 设置编码格式
     * @param $value
     */
    protected function _setCode($value){
        $this->_code = $value;
    }

    /**
     * 套接字
     * @return null
     */
    protected function _getSocket(){
        return $this->_socket;
    }

    /**
     * 设置套接字
     * @param $value
     */
    protected function _setSocket($value){
        $this->_socket = $value;
    }

}