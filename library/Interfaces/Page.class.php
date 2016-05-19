<?php
namespace Swiftx\DataBase\Interfaces;

/**
 * -----------------------------------------------------------------------------------------------------------------------------
 * 数据库栏目对象
 * -----------------------------------------------------------------------------------------------------------------------------
 * @author      胡永强 <odaytudio@gmail.com>
 * @since       2014-11-17
 * @copyright   Copyright (c) 2014-2015 Swiftx Inc.
 * ---------------------------------------------------------------------------------------------------------------------
 * @property int $Count	总记录数
 * @property int $Total	总页数
 * @property int $NumPer	每页显示记录数
 * @property int $Current	当前页码
 * @property int $Data		分页内容载入
 * @property int $Valid	迭代器标记
 */
interface Page extends \ArrayAccess, \Iterator{


}