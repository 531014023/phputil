<?php
/**
 * Created by PhpStorm.
 * User: dy
 * Date: 2018/6/12
 * Time: 18:00
 */

namespace app\common\command;


use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\Db;


class Create extends Command
{
    protected function configure()
    {
        $this->setName('create')
            ->addOption('table', 't', Option::VALUE_REQUIRED, 'table name without prefix', null)
            ->addOption('menu', 'm', Option::VALUE_REQUIRED, 'menu name without prefix', null)
            ->setDescription('mark');
    }

    protected function execute(Input $input, Output $output)
    {
        $table = $input->getOption('table')?:'';
        $table = ucfirst($table);
        $menu = $input->getOption('menu')?:'';
        $res = $this->createTemplate($table);
        $result = $this->createMenu($table,$menu);
        if(!$res)
        {
            $output->writeln('create tpl error!');
        }
        if(!$result)
        {
            $output->writeln('create menu error!');
        }
        if($res && $result)
        {
            $output->writeln('success');
        }
    }

    protected function createTemplate($table)
    {
        if(empty($table))
        {
            die('表名不能为空');
        }
        $this->createView($table);
        $this->createValidate($table);
        $this->createModel($table);
        $this->createLogic($table);
        $this->createController($table);
        return true;
    }

    protected function createValidate($table)
    {
        $tpl = file_get_contents(str_replace('\\','/',APP_PATH . 'common/command/tpl/validate/ClassName.php'));
        $tpl = str_replace('ClassName',$table,$tpl);
        file_put_contents(APP_PATH . 'admin/validate/'.$table.'.php',$tpl);
    }

    protected function createModel($table)
    {
        $tpl = file_get_contents(str_replace('\\','/',APP_PATH . 'common/command/tpl/model/ClassName.php'));
        $tpl = str_replace('ClassName',$table,$tpl);
        file_put_contents(APP_PATH . 'admin/model/'.$table.'.php',$tpl);
    }

    protected function createLogic($table)
    {
        $tpl = file_get_contents(APP_PATH . 'common/command/tpl/logic/ClassName.php');
        $tpl = str_replace('ClassName',$table,$tpl);
        $tpl = str_replace('className',lcfirst($table),$tpl);
        file_put_contents(APP_PATH . 'admin/logic/'.$table.'.php',$tpl);
    }

    protected function createController($table)
    {
        $tpl = file_get_contents(APP_PATH . 'common/command/tpl/controller/ClassName.php');
        $tpl = str_replace('ClassName',$table,$tpl);
        $tpl = str_replace('className',lcfirst($table),$tpl);
        file_put_contents(APP_PATH . 'admin/controller/'.$table.'.php',$tpl);
    }

    protected function createView($table)
    {
        $lower = strtolower($table);
        $str = [];
        $index = 0;
        for ($i = 0;$i<strlen($lower);$i++)
        {
            $l = substr($lower,$i,1);
            $u = substr($table,$i,1);
            if($l != $u && $i != 0)
            {
                $str[] = substr($lower,$index,$i - $index);
                $index = $i;
            }
            if($i == strlen($lower) - 1)
            {
                $str[] = substr($lower,$index,$i + 1 - $index);
                $index = $i;
            }
        }
        $dir = count($str) >1 ? join('_',$str) : $str[0];
        if(!is_dir(APP_PATH . 'admin/view/'.$dir))
        {
            mkdir(APP_PATH . 'admin/view/'.$dir);
        }
        $index_tpl = file_get_contents(APP_PATH . 'common/command/tpl/view/class_name/index.html');
        $edit_tpl = file_get_contents(APP_PATH . 'common/command/tpl/view/class_name/edit.html');
        $fields = $this->getDbFiled($dir);
        $index_tpl = $this->tpl_list($fields,$index_tpl);
        $edit_tpl = $this->edit_tpl($fields,$edit_tpl);
        file_put_contents(APP_PATH . 'admin/view/'.$dir.'/index.html',$index_tpl);
        file_put_contents(APP_PATH . 'admin/view/'.$dir.'/edit.html',$edit_tpl);
    }

    protected function tpl_list($fields,$index_tpl)
    {
        $th = '';
        $td = '';
        !$fields && $fields = [];
        foreach ($fields as $v)
        {
            if(empty($v['comment']))
            {
                continue;
            }
            if(strpos($v['name'],'img') !== false && $v['field'] == 'int')
            {
                $th .= "\t<th>{$v['comment']}</th>".PHP_EOL;
                $td .= "\t<td><img class=\"admin-list-img-size\" src=\"{\$vo.{$v['name']}|get_picture_url}\"/></td>".PHP_EOL;
            }
            if(in_array($v['field'],['varchar','char','int','decimal','float']))
            {
                $th .= "\t<th>{$v['comment']}</th>".PHP_EOL;
                $td .= "\t<td>{\$vo->{$v['name']}}</td>".PHP_EOL;
            }

            if($v['field'] == 'text')
            {
                $th .= "\t<th>{$v['comment']}</th>".PHP_EOL;
                $td .= "\t<td style='max-width: 200px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;'>{\$vo->{$v['name']}}</td>".PHP_EOL;
            }
        }
        $index_tpl = str_replace('<th>标题</th>',$th,$index_tpl);
        $index_tpl = str_replace('<td>{$vo.name}</td>',$td,$index_tpl);
        return $index_tpl;
    }

    protected function edit_tpl($fields,$edit_tpl)
    {
        $row = '';
        !$fields && $fields = [];
        foreach ($fields as $v)
        {
            if(empty($v['comment']))
            {
                continue;
            }
            if(in_array($v['field'],['varchar']) && strpos($v['name'],'imgs') === false)
            {
                $row .= <<<php
        <div class="col-md-6">
            <div class="form-group">
              <label>{$v['comment']}</label>
              <span class="">（{$v['comment']}）</span>
              <input class="form-control" name="{$v['name']}" placeholder="请输入{$v['comment']}" value="{\$info['{$v['name']}']|default=''}" type="text">
            </div>
         </div>
php;
            }
            if(in_array($v['field'],['int','decimal','float']) && strpos($v['name'],'img') === false && strpos($v['name'],'status') === false)
            {
                $row .=<<<php
    <div class="col-md-6">
            <div class="form-group">
              <label>{$v['comment']}</label>
              <span class="">（{$v['comment']}）</span>
              <input class="form-control" name="{$v['name']}" placeholder="请输入{$v['comment']}" value="{\$info['{$v['name']}']|default=''}" type="number">
            </div>
         </div>
php;
            }
            if(strpos($v['name'],'img') !== false && $v['field'] == 'int')
            {
                $row .= <<<php
        <div class="col-md-6">
            <div class="form-group">
                <label>{$v['comment']}</label>
                <span class="">（{$v['comment']}）</span>
                <br/>
                {assign name="{$v['name']}" value="\$info.{$v['name']}|default='0'" /}
                {:widget('file/index', ['name' => '{$v['name']}', 'value' => \${$v['name']}, 'type' => 'img'])}
            </div>
          </div>
php;
            }
            if(in_array($v['field'],['varchar']) && strpos($v['name'],'imgs') !== false)
            {
                $row .= <<<php
        <div class="col-md-6">
            <div class="form-group">
                <label>{$v['comment']}</label>
                <span class="">（{$v['comment']}）</span>
                <br/>
                {assign name="{$v['name']}" value="\$info.{$v['name']}|default='0'" /}
                {:widget('file/index', ['name' => '{$v['name']}', 'value' => \${$v['name']}, 'type' => 'imgs'])}
            </div>
          </div>
php;
            }
            if($v['field'] == 'text')
            {
                $row .= <<<php
          <div class="col-md-12">
            <div class="form-group">
                <label>{$v['comment']}</label>
                <textarea class="form-control textarea_editor" name="{$v['name']}" placeholder="请输入{$v['comment']}">{\$info['{$v['name']}']|default=''}</textarea>
                {:widget('editor/index', array('name'=> '{$v['name']}','value'=> ''))}
            </div>
          </div>
php;
            }
            if(strpos($v['name'],'status') !== false && $v['field'] == 'int')
            {
                $row .= <<<php
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{$v['comment']}</label>
                        <span class="">（{$v['comment']}）</span>
                        <br/>
                        {assign name='{$v['name']}' value="\$info['{$v['name']}']|default=''"}
                        <label>
                            <input type="radio" name="{$v['name']}" value="0" {eq name="{$v['name']}" value="0"} checked {/eq} />
                            {$v['comment']}
                        </label>
                        <label>
                            <input type="radio" name="{$v['name']}" value="1" {eq name="{$v['name']}" value="1"} checked {/eq}  />
                            {$v['comment']}
                        </label>
                    </div>
                </div>
php;
            }
        }
        $edit_tpl = str_replace('<tpl>',$row,$edit_tpl);
        return $edit_tpl;
    }

    protected function createMenu($table,$menu)
    {
        if(empty($table) || empty($menu))
        {
            die('表名或菜单名为空');
        }
        $data['name'] = $menu."管理";
        $data['url'] = lcfirst($table) . '/index';
        $data['module'] = 'admin';
        $info = Db::name('menu')->where($data)->find();
        if(!empty($info))
        {
            return true;
        }
        $pid = Db::name('menu')->insertGetId($data);
        $dataAll = [
            [
                'name'=>$menu."添加",
                'url'=>lcfirst($table) . '/add',
                'is_hide'=>1,
                'module'=>'admin',
                'pid'=>$pid
            ],
            [
                'name'=>$menu."编辑",
                'url'=>lcfirst($table) . '/edit',
                'is_hide'=>1,
                'module'=>'admin',
                'pid'=>$pid
            ],
            [
                'name'=>$menu."状态",
                'url'=>lcfirst($table) . '/setStatus',
                'is_hide'=>1,
                'module'=>'admin',
                'pid'=>$pid
            ],
            [
                'name'=>$menu."排序",
                'url'=>lcfirst($table) . '/setSort',
                'is_hide'=>1,
                'module'=>'admin',
                'pid'=>$pid
            ],
        ];
        return Db::name('menu')->insertAll($dataAll);
    }

    protected function getDbFiled($table)
    {
        if(empty($table))
        {
            die('表名为空');
        }
        $table  = config('database.prefix') . $table;
        $database = config('database.database');
        $sql = "select column_name as name,column_comment as comment,data_type as field from information_schema.COLUMNS where table_name='{$table}' and TABLE_SCHEMA='{$database}';";
        $field = Db::query($sql);
        if(empty($field))
        {
            die('未查询到该表的结构');
        }
        return $field;
    }
}