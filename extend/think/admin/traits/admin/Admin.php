<?php

namespace think\admin\traits\admin;

use think\Exception;

use think\Model;
use think\Request;
use think\facade\Db;
use think\admin\Time;

trait Admin
{
    public function _filter($request)
    {
        $searchKey = input('searchKey');
        $query = [];
        if ($searchKey) {
            $query[] = ['name|id|pid|url', 'like', "%{$searchKey}%"];
        }

        $search_code = input('search_code');
        if (!$search_code) $search_code = $request->admin->code;

        $type = input('type');
        if ($type) {
            $query[] = ['type', 'in', $type];
        }
        return $query;
    }

    public function insert()
    {
        $data = input('post.');
        $data['password'] = md5($data['password']);
        $pk = Db::name('admin')->insert($data);
        return $this->doSuccess('新增成功');
    }

    public function getInfo()
    {
        return $this->doSuccess('ok', $this->admin);
        # code...
    }

    public function getAll()
    {
        $Admin = new \think\admin\model\Admin();

        $request = $this->request;
        $query = [];
        $code = input('code');
        if ($code) {
            $query[] = ['code', 'like', $code . "%"];
        } elseif ($request->admin->code) {
            $query[] = ['code', 'like', $request->admin->code . "%"];
        }

        $options = $Admin
            ->field('name,id,code,name as label,id as `value`')
            ->where($query)
            ->where('id', '>', 0)
            ->select();
        $r['options'] = $options;
        return $this->doSuccess('ok', $r);
    }

    public function changePwd()
    {


        $oldpwd = input('oldpwd');
        $newpwd = input('newpwd');
        $newpwd2 = input('newpwd2');
        if ($newpwd) {
            if (md5($oldpwd) != $this->admin['password']) {
                return $this->doError('旧密码错误');
            }
            if ($newpwd != $newpwd2) {
                return $this->doError('两次新密码输入不一致');
            }
            $vo['password'] = md5($newpwd);
        }


        Db::name('admin')->where('id', $this->admin_id)->update($vo);
        return $this->doSuccess('ok');
    }

    public function restPwd()
    {
        $id = input('id');
        $up['password'] = md5('88888888');
        $vo = Db::name('admin')->where('id', $id)->update($up);
        return $this->doSuccess('ok');
        # code...
    }

    public function profile()
    {
        $model = $this->getModel();
        $vo = $model->where('id', request()->admin_id)->find();
        return $this->doSuccess('ok', $vo);
    }

    public function doProfile()
    {
        $vo['name'] = input('name');
        $vo['mobile'] = input('mobile');
        $vo['head'] = input('head');


        $oldpwd = input('oldpwd');
        $newpwd = input('newpwd');
        $newpwd2 = input('newpwd2');
        if ($newpwd) {
            if (md5($oldpwd) != request()->admin['password']) {
                return $this->doError('旧密码错误');
            }
            if ($newpwd != $newpwd2) {
                return $this->doError('两次新密码输入不一致');
            }
            $vo['password'] = md5($newpwd);
        }


        Db::name('admin')->where('id', request()->admin_id)->update($vo);
        return $this->doSuccess('ok');
        # code...
    }

}