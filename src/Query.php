<?php

namespace think\query_helper;

/**
 * 扩展ThinkPHP5查询类
 */
class Query extends \think\db\Query
{
    /**
     * 查询辅助
     * @param \Closure $closure
     * @return Query
     */
    public function queryHelper(\Closure $closure)
    {
        $this->where(function ($query) use ($closure) {
            $queryHelper = new QueryHelper($query);
            $closure($queryHelper, $query);
        });
        return $this;
    }
}