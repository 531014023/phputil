<?php
/**
 * Created by PhpStorm.
 * User: dy
 * Date: 2018/6/12
 * Time: 22:00
 */

namespace app\admin\logic;

class ClassName extends AdminBase
{
    public function classNameList($where = [], $field = true, $order = '', $page = 0)
    {
        return $this->modelClassName->getList($where,$field,$order,$page);
    }

    public function _list($where = [], $field = true, $order = '', $page = false)
    {
        return $this->modelClassName->getList($where,$field,$order,$page);
    }

    public function classNameEdit($param = [])
    {
        $validate_res = $this->validateClassName->check($param);
        if(!$validate_res)
        {
            return [RESULT_ERROR, $this->validateClassName->getError()];
        }

        $result = $this->modelClassName->setInfo($param);

        $handle_text = empty($param['id']) ? '新增' : '编辑';

        $result && action_log($handle_text, $handle_text . '，param：'.http_build_query($param) );

        return $result ? [RESULT_SUCCESS, '操作成功', url('index')] : [RESULT_ERROR, $this->modelClassName->getError()];
    }

    public function classNameInfo($where = [], $field = true)
    {
        return $this->modelClassName->getInfo($where, $field);
    }

    public function getWhere($data = [])
    {
        $where = [];

        return $where;
    }
}