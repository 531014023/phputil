<?php
/**
 * 加载类文件(无视tp5按模块加载规则可跨模块加载文件)
 * @param string $name 加载的文件名
 * @param string $model 加载的文件所在模块
 * @param string $layer 加载的文件所在层级
 * @return mixed|null
 */
function loader($name='',$model='common',$layer='logic')
{
    $class = config('app_namespace')."\\".strtolower($model)."\\".strtolower($layer)."\\".ucfirst($name);
    $request = request();

    $request->__isset($class) ?: $request->bind($class, new $class());

    return $request->__get($class);
}

/**
 * 优化在循环中查询数据库
 * @param array $list 最终列表
 * @param null $key 关联字段的key
 * @param null $model 查询模型
 * @param string $id 目标模型的关联key
 * @param string $field 目标模型的字段
 * @param null $res_key 最终展示的字段key
 * @param null $value 单独取出的字段
 * @return array
 */
function getValByKey(&$list = [], $key = null, $model = null, $id = 'id',$field = '*', $res_key = null,$value = null)
{
    empty($res_key) && $res_key = $key;
    $ids = [];
    foreach ($list as $k=>$v)
    {
        $ids[] = $v[$key];
    }
    $map[$id] = ['in',$ids];
    $_list = $model->field($field)->where($map)->select();
    foreach ($list as $ke=>$val)
    {
        foreach ($_list as $k=>$v)
        {
            if($val[$key] == $v[$id])
            {
                $list[$ke][$res_key] = empty($value) ? $v : $v[$value];
            }
        }
    }
    return $list;
}

/**获取url完整域名(仅适用于onebase框架)
 * @param null $path 可以是资源Id
 * @param bool $resource_type 资源类型 默认为图片,false为文件
 * @return string
 */
function getUrl($path = null, $resource_type = true)
{
    if(empty($path))
    {
        return getUrl(URL_ROOT . '/static/module/admin/img/onimg.png');
    }
    if(is_array($path))//防止数组误操作
    {
        return $path;
    }
    if(is_numeric($path))
    {
        if($resource_type) {
            $path = get_picture_url($path);
        }else{
            $path = get_file_url($path);
        }
    }
    if(substr($path,0,4) == 'http')
    {
        return $path;
    }else
    {
        if (strpos($path, 'upload') === false && strpos($path, 'static') === false) {
            $path = URL_ROOT . '/upload/picture/' . $path;
        }
        $yun_url = config('static_domain');
        if (empty($yun_url)) {
            $url = DOMAIN . $path;
        } else {
            $url = $yun_url . $path;
        }
    }
    return $url;
}