<?php
namespace Swiftx\DataBase\Interfaces;
use Swiftx\DataBase\Connect;
use Swiftx\DataBase\Picker;
use Swiftx\DataBase\Exception;
use Swiftx\System\Object;

/**
 * 数据库对象
 * @author      胡永强 <odaytudio@gmail.com>
 * @since       2015-12-08
 * @copyright   Copyright (c) 2014-2015 swiftx Inc. (http://swiftx.oday.me/)
 *
 * @throws Exception 301 数据库连接失败
 */
abstract class Driver extends Object {

    /** @var Connect  */
    protected $_connect = null;
    /** @var mixed|null  */
    protected $_handle = null;
    /** @var bool|array */
    protected $_debug = false;

    /**
     * 连接到数据库实例
     * @param Connect $connect
     */
    public function __construct(Connect $connect){
        $this->_connect = $connect;
        $this->_handle = $this->Connect();
    }

    /**
     * 查看调试信息
     * @return array|bool
     */
    protected function _getDebug(){
        return $this->_debug;
    }

    /**
     * 设置调试状态
     * @param bool $value
     */
    protected function _setDebug($value){
        $this->_debug = $value?array():false;
    }

    /**
     * 进行数据库连接
     * @return mixed
     * @throws Exception 301 数据库连接失败
     */
    abstract protected function Connect();

    /**
     * 数据库查询
     * @param $sql
     * @return array
     */
    abstract public function Query($sql);

    /**
     * 获取一条记录
     * @param Dialect $sql
     * @return array
     */
    abstract public function QueryRow(Dialect $sql);

    /**
     * 获取多条记录
     * @param Dialect $sql      数据库语句
     * @param int $number
     * @param int $start
     * @return array
     */
    abstract public function QueryRows(Dialect $sql, $number=0, $start = 0);

    /**
     * 获取一个查询值
     * @param Dialect $sql
     * @param string $name
     * @return mixed
     */
    abstract public function QueryValue(Dialect $sql, $name);

    /**
     * 进行分页查询
     * @param Dialect $sql
     * @param int $current
     * @param int $number
     * @return Page
     */
    abstract public function Page(Dialect $sql, $current, $number);

    /**
     * 统计记录数
     * @param Dialect|string $sql
     * @return int
     */
    abstract public function Count($sql);

    /**
     * 执行Sql语句
     * @param string $sql
     * @return int
     */
    abstract public function Excuse($sql);

    /**
     * 执行插入操作
     * @param Dialect|string $sql
     * @param array $data
     * @return int
     */
    abstract public function Insert($sql, array $data=array());

    /**
     * 执行修改操作
     * @param Dialect|string $sql
     * @param array $data
     * @return int
     */
    abstract public function Update($sql, array $data=array());

    /**
     * 执行删除操作
     * @param Dialect|string $sql
     * @throws Exception
     * @return int
     */
    abstract public function Delete($sql);

    /**
     * 数据表清空操作
     * @param string $table
     * @return bool
     */
    abstract public function Clean($table);

    /**
     * 事物方式执行
     * @param mixed $excuse
     * @param null $success
     * @param null $error
     * @return mixed
     */
    abstract public function Transaction($excuse, $success=null, $error=null);

    /**
     * 创建拾取器
     * @return Picker
     */
    abstract function NewPicker();

    /**
     * 创建Sql构造器
     * @return Dialect
     */
    abstract function NewSql();

}