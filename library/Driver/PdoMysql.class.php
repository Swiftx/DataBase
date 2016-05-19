<?php
namespace Swiftx\DataBase\Driver;
use Swiftx\DataBase\Dialect\MySql;
use \Swiftx\DataBase\Interfaces\Page;
use Swiftx\DataBase\Interfaces\Dialect;
use Swiftx\DataBase\Interfaces\Driver;
use Swiftx\DataBase\Exception;
use PDOException;
use PDO;
use Swiftx\DataBase\Picker;
use Swiftx\Tools\Debug;

/**
 * 数据库对象
 * @author      胡永强 <odaytudio@gmail.com>
 * @since       2014-11-06
 * @copyright   Copyright (c) 2014-2015 Swiftx Inc.
 */
class PdoMysql extends Driver {

    /** @var PDO */
    protected $_handle;

    /**
     * 进行数据库连接
     * @throws Exception 301 数据库连接失败
     * @return PDO
     */
    protected function Connect(){
        try{
            return new PDO(
                'mysql:host='.$this->_connect->Host.';dbname='.$this->_connect->Name,
                $this->_connect->Username, $this->_connect->Password,
                [
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_CASE => false,
                    PDO::CASE_UPPER => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND =>  'set names \''.$this->_connect->Code.'\';'
                ]
            );
        } catch (PDOException $e){
            throw new Exception('数据库连接异常',301, $e);
        }
    }

    /**
     * 数据库查询
     * @param string $sql
     * @throws Exception
     * @return array
     */
    public function Query($sql){
        Debug::Out('Query:'.$sql);
        if(is_array($this->_debug))
            $this->_debug[] = $sql;
        if($result = $this->_handle->query($sql))
            return $result->fetchAll(PDO::FETCH_ASSOC);
        throw new Exception('Sql语句执行失败！',302);
    }

    /**
     * 获取一条记录
     * @param Dialect $sql
     * @param int $index
     * @return array
     */
    public function QueryRow(Dialect $sql, $index=1){
        $result = $this->Query($sql->Row($index));
        return empty($result)?null:$result[0];
    }

    /**
     * 获取多条记录
     * @param Dialect $sql 数据库语句
     * @param int $number
     * @param int $start
     * @return array
     */
    public function QueryRows(Dialect $sql, $number=0, $start = 0){
        if($number == 0) return $this->Query($sql->Rows);
        return $this->Query($sql->Rows($number, $start));
    }

    /**
     * 获取一个查询值
     * @param Dialect $sql
     * @param string $name
     * @return mixed
     */
    public function QueryValue(Dialect $sql, $name){
        // TODO: Implement QueryValue() method.
    }

    /**
     * 进行分页查询
     * @param Dialect $sql
     * @param int $current
     * @param int $number
     * @return Page
     */
    public function Page(Dialect $sql, $current, $number){
        // TODO: Implement Page() method.
    }

    /**
     * 统计记录数
     * @param Dialect|string $sql
     * @return int
     */
    public function Count($sql){
        if($sql instanceof Dialect) $sql = $sql->Rows;
        $sql = 'SELECT COUNT(*) as Count FROM ('.$sql.') as DataTable';
        $result = $this->Query($sql);
        return $result[0]['Count'];
    }

    /**
     * 执行Sql语句
     * @param string $sql
     * @return int
     */
    public function Excuse($sql){
        Debug::Out('Excuse:'.$sql);
        if(is_array($this->_debug))
            $this->_debug[] = $sql;
        return $this->_handle->exec($sql);
    }

    /**
     * 数据表清空操作
     * @param string $table
     * @return bool
     */
    public function Clean($table)
    {
        // TODO: Implement Clean() method.
    }

    /**
     * 创建对象拾取器
     * @return \Swiftx\DataBase\Picker
     */
    public function NewPicker(){
        return new Picker($this);
    }

    /**
     * 创建Sql构造器
     * @return Dialect
     */
    public function NewSql(){
        return new MySql();
    }

    /**
     * 执行插入操作
     * @param Dialect|string $sql
     * @param array $data
     * @return int
     * @throws Exception
     */
    public function Insert($sql, array $data=array()){
        if($sql instanceof Dialect) {
            if($this->Excuse($sql->Insert($data)))
                return $this->_handle->lastInsertId();
            throw new Exception('Sql语句执行不正确',500);
        }
        if(!is_string($sql))
            throw new Exception('参数类型不正确',500);
        foreach ($data as $key => $value)
            $sql = str_replace('${:' . $key . '}', $value, $sql);
        if($this->Excuse($sql))
            return $this->_handle->lastInsertId();
        throw new Exception('Sql语句执行不正确',500);
    }


    /**
     * 执行修改操作
     * @param Dialect|string $sql
     * @param array $data
     * @return int
     * @throws Exception
     */
    public function Update($sql, array $data=array()){
        if($sql instanceof Dialect)
            return $this->Excuse($sql->Update($data));
        if(!is_string($sql))
            throw new Exception('参数类型不正确',500);
        foreach ($data as $key => $value)
            $sql = str_replace('${:' . $key . '}', $value, $sql);
        return $this->Excuse($sql);
    }

    /**
     * 执行删除操作
     * @param Dialect|string $sql
     * @throws Exception
     * @return int
     */
    public function Delete($sql){
        if($sql instanceof Dialect)
            return $this->Excuse($sql->Delete);
        if(!is_string($sql))
            throw new Exception('参数类型不正确',500);
        return $this->Excuse($sql);
    }

    /**
     * 事物方式执行
     * @param mixed $excuse
     * @param null $success
     * @param null $error
     * @return mixed
     */
    public function Transaction($excuse, $success = null, $error = null)
    {
        // TODO: Implement Transaction() method.
    }


}
