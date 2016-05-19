<?php
namespace Swiftx\DataBase\Interfaces;
use Swiftx\System\Object;
use Swiftx\DataBase\Exception;
use Swiftx\Tools\Debug;

/**
 * 数据库方言接口
 * @author      胡永强 <odaytudio@gmail.com>
 * @since       2015-12-09
 * @copyright   Copyright (c) 2014-2015 Swiftx Inc.
 * @property string $Query
 * @property string $Row
 * @property string $Rows
 * @property string $Count
 * @property string $Delete
 * @property $this $Distinct
 */
abstract class Dialect extends Object {

	private $_limit = array();
	private $_table = array();
	private $_fields = array();
	private $_where = array();
	private $_order = array();
    private $_distinct = false;

    /**
     * 数据表全名
     * @param string $table
     * @param string|null $name
     * @return $this
     */
    public function Table($table, $name=null){
        $this->_table[$table] = $name;
        return $this;
    }

    /**
     * 构造插入语句
     * @param $args
     * @return string
     * @throws Exception
     */
    public function Insert($args){
        $sql = 'INSERT INTO ';
        foreach($this->_table as $key => $value)
            $sql .= $key.(empty($value)?',':' AS `'.$value.'`,');
        $sql = substr($sql,0,-1).' ';
        if(is_array($args)) $keys = array_keys($args);
        else $args = func_get_args();
        if(isset($keys)) $sql .= '(`'.implode('`,`', $keys).'`) ';
        $data = array();
        foreach ($args as $key => $value){
            if(is_string($value))
                $value = '\''.addslashes($value).'\'';
            else if($value === null) $value = 'NULL';
            else if(is_bool($value)) $value = $value?1:0;
            if(!is_int($value) and !is_float($value) and is_double($value))
                throw new Exception('数据只能是字符串整形，布尔或空',501);
            $data[] = $value;
        }
        return $sql.'VALUES ('.implode(',',$data).')';
    }

    /**
     * 消除重复项
     * @return $this
     */
    protected function _getDistinct(){
        $this->_distinct = true;
        return $this;
    }

    /**
     * 选择列（在原有基础上新增）
     * @param string $field
     * @param string $name
     * @return $this
     */
	public function Select($field, $name=null){
		$this->_fields[$field] = $name;
		return $this;
	}

    /**
     * 选定列
     * @param string[] 列名
     * @return $this
     */
	public function Fields(){
		$this->_fields = array();
		$args = func_get_args();
		if(is_array($args)){
			$this->_fields = $args;
			return $this;
		}
		foreach ($args as $value)
			$this->_fields[$value] = null;
		return $this;
	}

    /**
     * And条件命令
     * @throws Exception
     * @internal param \string[] $列名
     * @return $this
     */
	protected function _getAnd(){
        if(empty($this->_where))
            throw new Exception('首个条件不能添加AND',501);
        if(!is_array(end($this->_where)))
            throw new Exception('不能重复添加AND',501);
		$this->_where[] = 'AND';
		return $this;
	}

    /**
     * Or条件命令
     * @return $this
     * @throws Exception
     */
	protected function _getOr(){
        if(empty($this->_where))
            throw new Exception('首个条件不能添加OR',501);
        if(!is_array(end($this->_where)))
            throw new Exception('不能重复添加OR',501);
        $this->_where[] = 'OR';
        return $this;
	}

    /**
     * 增加查询条件
     * @param string[] 增加查询条件
     * @return $this
     */
	public function Where(){
		if(empty($this->_where)){
			$this->_where[] = func_get_args();
			return $this;
		}
        if(is_array(end($this->_where)))
            $this->_where[] = 'AND';
		$this->_where[] = func_get_args();
		return $this;
	}

    /**
     * 增加AND查询条件
     * @return $this
     * @throws Exception
     */
	public function AndWhere(){
        if(empty($this->_where)){
            $this->_where[] = func_get_args();
            return $this;
        }
        if(is_array(end($this->_where)))
            $this->_where[] = 'AND';
        else
            throw new Exception('不能重复添加And', 501);
        $this->_where[] = func_get_args();
        return $this;
	}

    /**
     * 增加OR查询条件
     * @return $this
     * @throws Exception
     */
	public function OrWhere(){
        if(empty($this->_where)){
            $this->_where[] = func_get_args();
            return $this;
        }
        if(is_array(end($this->_where)))
            $this->_where[] = 'OR';
        else
            throw new Exception('不能重复添加And', 501);
        $this->_where[] = func_get_args();
        return $this;
	}

    /**
     * 增加Order排序条件
     * @param string $field 列名
     * @param string $type 排序方式
     * @throws Exception
     * @return $this
     */
	public function OrderBy($field, $type){
		$type = strtoupper($type);
		if($type=='ASC' or $type=='DESC'){
			$this->_order[$field] = $type;
			return $this;
		}
        throw new Exception('参数Type只能是ASC或DESC', 501);
	}

    /**
     * 增加Limit查找条件
     * @param int $number
     * @param int $start
     * @return $this
     */
	public function Limit($number, $start=1){
        $this->_limit['Number'] = $number;
        $this->_limit['Start'] = $start-1;
    }

    /**
     * 生成查询语句
     * @return string
     */
    protected function _getQuery(){
        $sql = 'SELECT ';
        if($this->_distinct){
            $sql .= 'DISTINCT ';
        }
        if(empty($this->_fields)){
            $sql .= '*';
        } else {
            foreach($this->_fields as $key => $value)
                $sql .= $key.(empty($value)?',':' AS `'.$value.'`,');
            $sql = substr($sql,0,-1);
        }
        $sql .= ' FROM ';
        foreach($this->_table as $key => $value)
            $sql .= $key.(empty($value)?',':' AS `'.$value.'`,');
        $sql = substr($sql,0,-1);
        if(!empty($this->_where)){
            $sql .= ' WHERE';
            foreach($this->_where as $value) {
                $value = is_string($value) ? $value : $this->_whereSql($value);
                $sql = $sql.' '.$value;
            }
        }
        if(!empty($this->_group))
            $sql .= ' GROUP BY '.implode(',', $this->_group);
        if(!empty($this->_order)){
            $sql .= ' ORDER BY ';
            foreach($this->_order as $key => $value)
                $sql .= $key.' '.$value.',';
            $sql = substr($sql,0,-1);
        }
        if(isset($this->_limit['Number']))
            $sql .= ' LIMIT '.$this->_limit['Start'].','.$this->_limit['Number'];
        return $sql;
    }

    /**
     * 增加查询条件
     * @param array $args
     * @return string
     * @throws Exception
     */
    protected function _whereSql($args){
        switch (count($args)){
            case 1:
                return $args[0];
            case 2:
                $args[1] = strtoupper($args[1]);
                switch ($args[1]) {
                    case 'IS NULL':
                    case 'NULL':
                        return $args[0].' IS NULL';
                    case 'IS NOT NULL':
                    case '!NULL':
                        return $args[0].' IS NOT NULL';
                    default:
                        return $args[0].'='.(is_string($args[1])?'\''.$args[1].'\'':$args[1]);
                }
            case 3:
                $args[1] = strtoupper($args[1]);
                $args[2] = is_string($args[2])?'\''.$args[2].'\'':$args[2];
                switch ($args[1]) {
                    case '=':
                        return $args[0].' = '.$args[2];
                    case '<>':
                    case '!=':
                        return $args[0].' <> '.$args[2];
                    case '<':
                        return $args[0].' < '.$args[2];
                    case '<=':
                        return $args[0].' <= '.$args[2];
                    case '>':
                        return $args[0].' > '.$args[2];
                    case '>=':
                        return $args[0].' >= '.$args[2];
                    case 'LIKE':
                        return $args[0].' LIKE '.$args[2];
                    case 'NOT LIKE':
                    case '!LIKE':
                        return $args[0].' NOT LIKE '.$args[2];
                    case 'IS IN LIST':
                    case 'IS IN':
                    case 'IN':
                        if(is_array($args[2]))
                            $args[2] = implode('\',\'',$args[2]);
                        return $args[0].' IN (\''.$args[2].'\')';
                    case 'IS NOT IN LIST':
                    case 'IS NOT IN':
                    case 'NOT IN':
                    case '!IN':
                        if(is_array($args[2]))
                            $args[2] = implode('\',\'',$args[2]);
                        return $args[0].' NOT IN (\''.$args[2].'\')';
                }
                throw new Exception('参数格式不正确',500);
            case 4:
                $args[1] = strtoupper($args[1]);
                $args[2] = is_string($args[2])?'\''.$args[2].'\'':$args[2];
                $args[3] = is_string($args[3])?'\''.$args[3].'\'':$args[3];
                switch ($args[1]) {
                    case 'BETWEEN':
                        return $args[0].' BETWEEN '.$args[2].' AND '.$args[3];
                    case 'NOT BETWEEN':
                    case '!BETWEEN':
                        return $args[0].' NOT BETWEEN '.$args[2].' AND '.$args[3];
                    case 'IS IN LIST':
                    case 'IS IN':
                    case 'IN':
                        return $args[0].' IN (\''.$args[2].'\',\''.$args[3].'\')';
                    case 'IS NOT IN LIST':
                    case 'IS NOT IN':
                    case 'NOT IN':
                    case '!IN':
                        return $args[0].' NOT IN ('.$args[2].','.$args[3].')';
                }
                throw new Exception('参数格式不正确',500);
            default:
                $sql = '`'.array_shift($args).'`';
                $method = strtoupper(array_shift($args));
                foreach($args as &$value)
                    $value = is_string($value)?'\''.$value.'\'':$value;
                switch ($method) {
                    case 'IS IN LIST':
                    case 'IS IN':
                    case 'IN':
                        return $sql.' IN ('.implode(',', $args).')';
                    case 'IS NOT IN LIST':
                    case 'IS NOT IN':
                    case 'NOT IN':
                    case '!IN':
                        return $sql.' NOT IN ('.implode(',', $args).')';
                }
                throw new Exception('参数格式不正确',500);
        }
    }


    /**
     * 查询一行数据
     * @param int $line 获取获取第几行数据,默认为首行
     * @return string
     */
	public function Row($line){
        $limit =  $this->_limit;
        $this->Limit(1,$line);
		$sql = $this->Query;
        $this->_limit = $limit;
        return $sql;
	}

    /**
     * 查询第一条符合条件的记录
     * @return array
     */
    protected  function _getRow(){
        return $this->Row(1);
    }

    /**
     * 查询一组数据
     * @param int $number
     * @param int $start
     * @return string
     */
	public function Rows($number, $start=1){
        $limit =  $this->_limit;
        $this->Limit($number, $start);
        $sql = $this->Query;
        $this->_limit = $limit;
        return $sql;
	}

	/**
	 * 取所有查询记录
	 * @return array
	 */
	protected function _getRows(){
		return $this->Query;
	}
	
	/**
	 * 取一个查询值
     * @param $field
     * @return string
     */
	public function Value($field){
        $fields = $this->_fields;
        $sql = $this->Fields($field)->Row;
        $this->_fields = $fields;
        return $sql;
	}

	/**
	 * 统计可以查询到记录数
	 * @return string
	 */
	protected function _getCount(){
        $fields = $this->_fields;
        $this->_fields = array();
        $this->Select('count(*)', 'Count');
        $sql = $this->Row;
        $this->_fields = $fields;
        return $sql;
	}

    /**
     * 更新筛选内容
     * @param array|string $args
     * @param bool|string $value
     * @throws Exception
     * @return string
     */
	public function Update($args, $value=false){
        if(is_string($args))
            $args = array($args => $value);
        else if(!is_array($args))
            throw new Exception('参数只能是字符串或者数组',501);
        if(empty($this->_table))
            throw new Exception('未设置更新的目标数据表',501);
        if(count($this->_table) > 1)
            throw new Exception('不能对连表查询的结果集进行更新操作',501);
        $sql = 'UPDATE ';
        foreach($this->_table as $key => $value)
            $sql .= $key.(empty($value)?',':' AS `'.$value.'`,');
        $sql = substr($sql,0,-1).' SET ';
        $data = array();
        foreach ($args as $key => $value){
            if(is_string($value))
                $value = '\''.addslashes($value).'\'';
            else if($value === null) $value = 'NULL';
            else if(is_bool($value)) $value = $value?1:0;
            if(!is_int($value) and !is_float($value) and is_double($value))
                throw new Exception('数据只能是字符串整形，布尔或空',501);
            $data[] = '`'.addslashes($key).'`='.$value;
        }
        $sql .= implode(',', $data);
        if(!empty($this->_where)){
            $sql .= ' WHERE';
            foreach($this->_where as $value) {
                $value = is_string($value) ? $value : $this->_whereSql($value);
                $sql = $sql.' '.$value;
            }
        }
        return $sql;
	}

    /**
     * 执行删除操作
     * @return string
     * @throws Exception
     */
	protected function _getDelete(){
        if(empty($this->_table))
            throw new Exception('未设置删除的目标数据表',501);
        if(count($this->_table) > 1)
            throw new Exception('不能对连表查询的结果集进行删除操作',501);
        $sql = 'DELETE FROM ';
        foreach($this->_table as $key => $value)
            $sql .= $key.(empty($value)?',':' AS `'.$value.'`,');
        $sql = substr($sql,0,-1).' WHERE ';
        foreach($this->_where as $value) {
            $value = is_string($value) ? $value : $this->_whereSql($value);
            $sql = $sql.' '.$value;
        }
        return $sql;
	}




	/**
	 * 获取表的字段结构
     * @return string
     * @throws Exception
     */
	public function _getColumns(){
        if(empty($this->_table))
            throw new Exception('未设置目标数据表',501);
        if(count($this->_table) > 1)
            throw new Exception('不能对连表查询的结果集进行操作',501);
		return 'SHOW FULL COLUMNS FROM '.addslashes($this->_table[0]);
	}

}