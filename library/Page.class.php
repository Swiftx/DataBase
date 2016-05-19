<?php
namespace Swiftx\DataBase;
/**
 * -----------------------------------------------------------------------------------------------------------------------------
 * 数据库分页对象
 * -----------------------------------------------------------------------------------------------------------------------------
 * @author      胡永强 <odaytudio@gmail.com>
 * @since       2014-11-17
 * @copyright   Copyright (c) 2014-2015 Swiftx Inc.
 * -----------------------------------------------------------------------------------------------------------------------------
 */
class Page implements \ArrayAccess,\Iterator {
	
	private $_count;			// 总记录数
	private $_total;			// 总页数
	private $_numPer;			// 每页显示记录数
	private $_current;			// 当前页码
	private $_data;			// 分页内容载入
	private $_valid;			// 迭代器标记
	private $_sqlBuilder;		// 查询构造器
	
	/**
	 +----------------------------------------------------------
	 * 获取该模型所有实例
	 +----------------------------------------------------------
	 * @param int $current 当前页页码
	 * @param int $numPer 每页显示记录数
	 * @param int $count 总记录数
	 * @param array 分页数据 所属数据表 
	 +----------------------------------------------------------
	 */
	public function __construct($current, $numPer, $SqlBuilder){
		$this->_count = $SqlBuilder->Count;
		$this->_sqlBuilder = $SqlBuilder;
		$this->_numPer = $numPer;
		$this->_total = intval($this->_count/$numPer);
		if($this->_count%$numPer > 0)
			$this->_total += 1;
		$this->_current = $current;
		$this->_data = $SqlBuilder->Rows(($current-1)*$numPer, $numPer);
	}

	/**
	 +----------------------------------------------------------
	 * 魔术方法Get
	 +----------------------------------------------------------
	 * @param String $name
	 +----------------------------------------------------------
	 */
	public function __get($name){
		$method = '_get'.$name;
		return $this->$method();
    }

	/**
	 +----------------------------------------------------------
	 * 数组查看一条分页数据
	 +----------------------------------------------------------
	 * @param string $offset 列名
	 +----------------------------------------------------------
	 */
	public function offsetExists($offset){
		return isset($this->_data[$offset]);
	}

	/**
	 +----------------------------------------------------------
	 * 数组模式读取一行数据
	 +----------------------------------------------------------
	 * @param string $offset 列名
	 +----------------------------------------------------------
	 */
	public function offsetGet($offset){
		return $this->_data[$offset];
	}

	/**
	 +----------------------------------------------------------
	 * 数组模式设置字段的值
	 +----------------------------------------------------------
	 * @param string $offset 列名
	 * @param $value $value 值
	 +----------------------------------------------------------
	 */
	public function offsetSet($offset, $value){
		return $this->_data[$offset] = $value;
	}

	/**
	 +----------------------------------------------------------
	 * 对象数组模式删除方法,禁用！
	 +----------------------------------------------------------
	 * @param string $offset 表全名
	 +----------------------------------------------------------
	 */
	public function offsetUnset($offset){
		unset($this->_data[$offset]);
	}

	/**
	 +----------------------------------------------------------
	 * 将迭代器的指针移向第一个元素。类似于数组操作函数reset()
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function rewind(){
		reset($this->_data);
		$this->_valid = (empty($this->_data))?false:true;
	}

	/**
	 +----------------------------------------------------------
	 * 类似于数组操作函数current()。返回迭代的当前元素
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	 */
	public function current(){
		return current($this->_data);
	}

	/**
	 +----------------------------------------------------------
	 * 返回当前迭代器元素的键名，类似于数组操作函数key()
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	public function key(){
		return key($this->_data);
	}
	
	/**
	 +----------------------------------------------------------
	 * 将指针移向迭代器的下一个元素，类似于数组操作函数next()
	 +----------------------------------------------------------
	 * @return null
	 +----------------------------------------------------------
	 */
	public function next(){
		$this->_valid = (next($this->_data)===false)?false:true;
	}

	/**
	 +----------------------------------------------------------
	 * 检测在执行了rewind()或是next()函数之后，当前值是否是一个有效的值
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	public function valid(){
		return $this->_valid;
	}

	/**
	 +----------------------------------------------------------
	 * 总记录数
	 +----------------------------------------------------------
	 * @return int
	 +----------------------------------------------------------
	 */
	protected function _getCount(){
		return $this->_count;
	}

	/**
	 +----------------------------------------------------------
	 * 总页数
	 +----------------------------------------------------------
	 * @return int
	 +----------------------------------------------------------
	 */
	protected function _getTotal(){
		return $this->_total;
	}

	/**
	 +----------------------------------------------------------
	 * 每页显示记录数
	 +----------------------------------------------------------
	 * @return int
	 +----------------------------------------------------------
	 */
	protected function _getNmePer(){
		return $this->_numPer;
	}
	
	/**
	 +----------------------------------------------------------
	 * 当前页码
	 +----------------------------------------------------------
	 * @return int
	 +----------------------------------------------------------
	 */
	protected function _getCurrent(){
		return $this->_current;
	}

	/**
	 +----------------------------------------------------------
	 * 获取页码内容
	 +----------------------------------------------------------
	 * @return int
	 +----------------------------------------------------------
	 */
	protected function _getToArray(){
		return $this->_data;
	}
	
	/**
	 +----------------------------------------------------------
	 * 是否是第一页
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	protected function _getIsFirst(){
		return ($this->_current==1)?true:false;
	}
	
	/**
	 +----------------------------------------------------------
	 * 是否是最后一页
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	 */
	protected function _getIsLast(){
		return ($this->_current==$this->_total)?true:false;
	}

}