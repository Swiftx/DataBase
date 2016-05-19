<?php
namespace Swiftx\DataBase;
use Swiftx\System\Object;
use Swiftx\DataBase\Interfaces\Dialect;
use Swiftx\DataBase\Interfaces\Driver;
/**
 * 数据库栏目对象
 * @author      胡永强 <odaytudio@gmail.com>
 * @since       2014-11-17
 * @copyright   Copyright (c) 2014-2015 Swiftx Inc.
 * @property-read array Row
 * @property-read array Rows
 * @property-read int Count
 * @property-read int Delete
 * @property-read array Columns
 * @method Picker Where() static::Where(string $name)
 * @method Picker AndWhere() static::AndWhere(string $name)
 * @method Picker OrWhere() static::OrWhere(string $name)
 * @method Picker Table() static::Table($table, $name=null)
 * @method Picker Select() static::Select($field, $name=null)
 * @method Picker Fields() static::Fields()
 * @method Picker OrderBy() static::OrderBy($field, $type)
 * @method Picker Limit() static::Limit($number, $start=1)
 * @property-read $this Distinct
 */
final class Picker extends Object {

	/** @var Driver  */
	protected $_Driver;
	/** @var Dialect  */
	protected $_Sql;

    /**
     * 查询构造器构造方法
	 * @param Driver $database
	 */
	public function __construct(Driver $database){
		$this->_Sql = $database->NewSql();
		$this->_Driver = $database;
	}

	/**
	 * 属性映射
	 * @access Attribute
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name){
		$method = '_get'.$name;
		if(method_exists($this,$method))
			return $this->$method();
		$this->_Sql->$name;
		return $this;
	}

	/**
	 * 方法映射
	 * @param $name
	 * @param array $args
	 * @return $this
	 */
	public function __call($name, array $args){
		call_user_func_array([$this->_Sql, $name], $args);
		return $this;
	}

	/**
	 * 查询单条记录
	 * @return array
	 */
	public function _getRow(){
		return $this->Row(1);
	}

	/**
	 * 查询多条记录
	 * @return array
	 */
	public function _getRows(){
		return $this->_Driver->QueryRows($this->_Sql);
	}

	/**
	 * 查询单条数据
	 * @param int $line
	 * @return array
	 */
	public function Row($line){
		$sql = $this->_Sql->Row($line);
		$result = $this->_Driver->Query($sql);
		if(count($result) == 0) return array();
		return $result[0];
	}

	/**
	 * 查询单条数据
	 * @param int $number
	 * @param int $start
	 * @return array
	 */
	public function Rows($number, $start=1){
		$sql = $this->_Sql->Rows($number, $start);
		return $this->_Driver->Query($sql);
	}

	/**
	 * 查询记录数
	 * @return int
	 */
	public function _getCount(){
		return $this->_Driver->Count($this->_Sql);
	}

	/**
	 * 获取一个记录值
	 * @param string $name
	 * @return mixed
	 */
	public function Value($name){
		$data = $this->Row;
		return $data?$data[$name]:null;
	}

	/**
	 * 执行数据更新
	 * @param string|array $args
	 * @param bool|string $value
	 * @return int
	 */
	public function Update($args, $value=false){
		if(!is_array($args)) $args = [$args=>$value];
		return $this->_Driver->Update($this->_Sql, $args);
	}

	/**
	 * 执行数据更新
	 * @param string|array $args
	 * @return int
	 */
	public function Insert($args){
		if(!is_array($args))
			return call_user_func_array([$this->_Driver,'Insert'], func_get_args());
		else return $this->_Driver->Insert($this->_Sql, $args);
	}

    /**
     * 查询记录数
     * @return int
     */
    protected function _getDelete(){
        return $this->_Driver->Delete($this->_Sql);
    }

	/**
	 * 添加消除重复指令
	 * @return int
	 */
	protected function _getDistinct(){
		$this->_Sql->Distinct;
		return $this;
	}

}
