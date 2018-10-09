<?php
/**
 * Created by PhpStorm.
 * User: dy
 * Date: 2018/6/12
 * Time: 22:02
 */

namespace app\admin\controller;

class ClassName extends AdminBase
{
    public function index()
    {
        $where = $this->logicClassName->getWhere($this->param);
        $this->assign('list', $this->logicClassName->classNameList($where));

        return $this->fetch();
    }

    public function common()
    {
        IS_POST && $this->jump($this->logicClassName->classNameEdit($this->param));
    }

    public function add()
    {
        $this->common();

        return $this->fetch('edit');
    }

    public function edit()
    {
        $this->common();

        $info = $this->logicClassName->classNameInfo(['id'=>$this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('edit');
    }

    public function setStatus()
    {
        $this->jump($this->logicAdminBase->setStatus('ClassName',$this->param));
    }

    /**
     * 排序
     */
    public function setSort()
    {

        $this->jump($this->logicAdminBase->setSort('ClassName', $this->param));
    }
}