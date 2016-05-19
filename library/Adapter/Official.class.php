<?php
namespace Swiftx\DataBase\Adapter;
use Swiftx\DataBase\Connect;
use Swiftx\DataBase\Dialect\MySql;
use Swiftx\DataBase\Interfaces\Driver;
use Swiftx\DataBase\Picker;

/**
 * ---------------------------------------------------------------------------------------------------------------
 * 数据库对象
 * ---------------------------------------------------------------------------------------------------------------
 * @author      胡永强 <odaytudio@gmail.com>
 * @since       2015-09-30
 * @copyright   Copyright (c) 2014-2015 swiftx Inc. (http://swiftx.oday.me/)
 * ---------------------------------------------------------------------------------------------------------------
 */
abstract class Official {

    /**
     * 服务器地址
     * @var string
     */
    public static $Address = null;

    /**
     * 数据库名称
     * @var
     */
    public static $DBName = null;

    /**
     * 用户名
     * @var
     */
    public static $Username = null;

    /**
     * 密码
     * @var
     */
    public static $Password = null;

    /**
     * 驱动程序
     * @var string
     */
    public static $Method = null;

    /**
     * 编码格式
     * @var string
     */
    public static $Code = 'utf8';

    /**
     * 数据库驱动
     * @var \Swiftx\DataBase\Driver\PdoMysql
     */
    protected static $_Driver = null;

    /**
     * 静态构造函数
     */
    public static function __static(){
        if(__CLASS__!=get_called_class()) {
            $classname = 'Swiftx\\DataBase\\Driver\\'.static::$Method;
            $connect = new Connect();
            $connect->Host = static::$Address;
            $connect->Name = static::$DBName;
            $connect->Username = static::$Username;
            $connect->Password = static::$Password;
            $connect->Code = static::$Code;
            static::$_Driver = new $classname($connect);
        }
    }

    /**
     * 获取驱动
     * @return Driver
     */
    public static function Driver(){
        return static::$_Driver;
    }

    /**
     * 生成Sql对象
     * @return Mysql
     */
    public static function NewSql(){
        return static::$_Driver->NewSql();
    }

    /**
     * 生成数据拾取器
     * @return Picker
     */
    public static function NewPicker(){
        return static::$_Driver->NewPicker();
    }

    /**
     * 获取表拾取器
     * @param string $name
     * @return Picker
     */
    public static function Table($name){
        return static::NewPicker()->Table($name);
    }

}