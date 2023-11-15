<?php
declare (strict_types=1);

namespace think\admin\model;

use think\Model;

class Admin extends Model
{
    public function checkAuth($token)
    {
        $admin = $this->where('token', $token)->findOrEmpty();
        if ($admin->isEmpty()) return false;
        return $admin;
    }
}