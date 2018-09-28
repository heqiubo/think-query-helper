# think-addons
The ThinkPHP5 queryHelper Package

## 安装
> composer require heqiubo/think-query-helper

## 配置
### 设置Query类
>将database.php中的query配置项修改为\\think\\query_helper\\Query
```
// database配置
'query'           => '\\think\\query_helper\\Query',
```

### 代码提示处理
>在模型基类中引入Query类，注释增加@mixin Query
```
<?php

namespace app\index\model;

use think\Model;
use think\query_helper\Query;

/**
 * @mixin Query
 */
class BaseModel extends Model
{
}
```

## 使用
### 示例

```

$data = (new Book())
    ->queryHelper(function (QueryHelper $helper) {
         // 从请求参数中获取name值，并从数据库中查询
         $helper->where('name');
    })
    ->select();
    
```

### 查询方法

```
where($field);          //指定AND查询条件
whereOr($field);        //指定OR查询条件
whereLike($field);      //指定Like查询条件
whereLikeOr($field);    //指定Like查询条件或
whereRange($field);     //范围查询
whereTime($field);      //时间查询
whereTimeRange($field); //查询当前时间在两个时间字段范围
```
