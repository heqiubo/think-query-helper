<?php

namespace think\query_helper;

use think\facade\Request;

/**
 * ThinkPHP5查询辅助类
 * Class QueryHelper
 * @package app\index\util
 */
class QueryHelper
{
    /**
     * Query查询对象
     * @var string
     */
    protected $queryObj = '';

    /**
     * 所有参数
     * @var array
     */
    protected $params = [];


    public function __construct(Query $queryObj)
    {
        $this->queryObj = $queryObj;
        $this->params = Request::param();
    }

    /**
     * 获取请求的字段
     * @param $name
     * @return
     */
    protected function getRequestField($name)
    {
        return strpos($name, '.') !== false ? explode('.', $name)[1] : $name;
    }

    /**
     * 查询区间请求字段
     * @param $name
     * @return array
     */
    protected function getRequestRangeField($name)
    {
        $start = $this->getRequestField($name) . '_start';
        $end = $this->getRequestField($name) . '_end';
        return [$start, $end];
    }

    /**
     * 判断参数是否存在
     * @param $name
     * @return bool
     */
    protected function hasParam($name)
    {
         return array_key_exists($name, $this->params) && $this->params[$name] !== '';
    }

    /**
     * 获取参数值
     * @param $name
     * @return mixed
     */
    protected function getParamValue($name)
    {
        return $this->hasParam($name) ? $this->params[$name] : '';
    }

    /**
     * 解析查询条件
     * @param $name /查询字段
     * @param $logic /查询逻辑 and or Like
     * @return QueryHelper
     */
    protected function parseWhere($name, $logic)
    {
        $reqField = $this->getRequestField($name);
        if ($this->hasParam($reqField)) {
            switch ($logic) {
                case 'and':
                    $this->queryObj->where($name, $this->getParamValue($reqField));
                    break;
                case 'or':
                    $this->queryObj->whereOr($name, $this->getParamValue($reqField));
                    break;
                case 'like':
                    $this->queryObj->whereLike($name, '%' . $this->getParamValue($reqField) . '%');
                    break;
                case 'like_or':
                    $this->queryObj->whereOr($name, 'like', '%' . $this->getParamValue($reqField) . '%');
                    break;
            }
        }
        return $this;
    }

    /**
     * 多字段模糊搜索
     * @param $names /请求字段（数组或字符串）
     * @return QueryHelper
     */
    public function muchFieldSearch($names)
    {
        $names = is_string($names) ? explode(',', $names) : $names;

        if ($this->hasParam('search_text')) {
            $searchText = $this->getParamValue('search_text');

            foreach ($names as $name) {
                $this->queryObj->whereOr($name, 'like', '%' . $searchText . '%');
            }
        }
        return $this;
    }

    /**
     * 指定AND查询条件
     * @param $name
     * @return $this
     */
    public function where($name)
    {
        return $this->parseWhere($name, 'and');
    }

    /**
     * 指定OR查询条件
     * @param $name
     * @return $this
     */
    public function whereOr($name)
    {
        return $this->parseWhere($name, 'or');
    }

    /**
     * 指定Like查询条件
     * @param $name
     * @return QueryHelper
     */
    public function whereLike($name)
    {
        return $this->parseWhere($name, 'like');
    }

    /**
     * 指定Like查询条件或
     * @param $name
     * @return QueryHelper
     */
    public function whereLikeOr($name)
    {
        return $this->parseWhere($name, 'like_or');
    }

    /**
     * 范围查询
     * @param $name
     * @return QueryHelper
     */
    public function whereRange($name)
    {
        list($startField, $endField) = $this->getRequestRangeField($name);

        if ($this->hasParam($endField)) {
            $this->queryObj->where($name, '<=', $this->getParamValue($endField));
        }

        if ($this->hasParam($startField)) {
            $this->queryObj->where($name, '>=', $this->getParamValue($startField));
        }
        return $this;
    }

    /**
     * 时间查询
     * @param $name
     * @return QueryHelper
     */
    public function whereTime($name)
    {
        $reqField = $this->getRequestField($name);
        if ($this->hasParam($reqField)) {
            $startTime = $this->getParamValue($reqField);
            $endTime = date('Y-m-d', strtotime($startTime) + 86400);

            $this->queryObj->whereBetweenTime($name, $startTime, $endTime);
        }
        return $this;
    }

    /**
     * 查询当前时间在两个时间字段范围
     * @param $name
     * @return QueryHelper
     */
    public function whereTimeRange($name)
    {
        list($reqStartField, $reqEndField) = $this->getRequestRangeField($name);

        $startTime = $this->getParamValue($reqStartField);
        $endTime = $this->getParamValue($reqEndField);

        if ($this->hasParam($reqStartField) && $this->hasParam($reqEndField)) {
            $this->queryObj->whereTime($name, 'between', [
                strtotime($startTime), strtotime($endTime) + 3600 * 24
            ]);
        } else {
            if ($this::hasParam($reqEndField)) {
                $this->queryObj->whereTime($name, '<=', $endTime);
            }
            if ($this->hasParam($reqStartField)) {
                $this->queryObj->whereTime($name, '>=', $startTime);
            }
        }
        return $this;
    }
}
