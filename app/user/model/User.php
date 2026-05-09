<?php


namespace app\user\model;

use think\Model;

/**
 * 后台用户模型
 * @package app\admin\model
 */
class User extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'admin_user';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    // 对密码进行加密（修复：使用 PHP 内置的 password_hash）
    public function setPasswordAttr($value)
    {
        return password_hash((string)$value, PASSWORD_DEFAULT);
    }

    // 获取注册ip
    public function setSignupIpAttr()
    {
        return get_client_ip(1);
    }

    /**
     * 用户登录
     * @param string $username 用户名
     * @param string $password 密码
     * @param bool $rememberme 记住登录
     * @return bool|mixed
     */
    public function login($username = '', $password = '', $rememberme = false)
    {
        $username = trim($username);
        $password = trim($password);

        // 匹配登录方式
        if (preg_match('/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/', $username)) {
            // 邮箱登录
            $map['email'] = $username;
        } elseif (preg_match('/^1\d{10}$/', $username)) {
            // 手机号登录
            $map['mobile'] = $username;
        } else {
            // 用户名登录
            $map['username'] = $username;
        }

        $map['status'] = 1;

        // 查找用户（修复：ThinkPHP 8 中 get() 只能接受字符串，使用 where()->find()）
        $user = $this->where($map)->find();
        if (!$user) {
            $this->error = '用户不存在或被禁用！';
        } else {
            // 检查是否分配用户组
            if ($user['role'] == 0) {
                $this->error = '禁止访问，原因：未分配角色！';
                return false;
            }
            // 检查是可登录后台
            if (!(new Role())->where(['id' => $user['role'], 'status' => 1])->value('access')) {
                $this->error = '禁止访问，用户所在角色未启用或禁止访问后台！';
                return false;
            }
            // 修复：使用 PHP 内置的 password_verify
            if (!password_verify((string)$password, $user['password'])) {
                $this->error = '账号或者密码错误！';
            } else {
                $uid = $user['id'];

                // 更新登录信息
                $user['last_login_time'] = request()->time();
                $user['last_login_ip'] = request()->ip();
                if ($user->save()) {
                    // 自动登录（修复：ThinkPHP 8 中 get() 只能接受字符串，使用 find()）
                    return $this->autoLogin($this->find($uid), $rememberme);
                } else {
                    // 更新登录信息失败
                    $this->error = '登录信息更新失败，请重新登录！';
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * 自动登录
     * @param object $user 用户对象
     * @param bool $rememberme 是否记住登录，默认7天
     * @return bool|int
     */
    public function autoLogin($user, $rememberme = false)
    {
        // 记录登录SESSION和COOKIES
        $auth = array(
            'uid' => $user->id,
            'group' => $user->group,
            'role' => $user->role,
            'role_name' => (new Role())->where('id', $user->role)->value('name'),
            'avatar' => $user->avatar,
            'username' => $user->username,
            'nickname' => $user->nickname,
            'last_login_time' => $user->last_login_time,
            'last_login_ip' => get_client_ip(1),
        );
        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));

        // 保存用户节点权限
        if ($user->role != 1) {
            $menu_auth = (new Role())->where('id', session('user_auth.role'))->value('menu_auth');
            $menu_auth = json_decode($menu_auth, true);
            if (!$menu_auth) {
                session('user_auth', null);
                session('user_auth_sign', null);
                $this->error = '未分配任何节点权限！';
                return false;
            }
        }

        // 记住登录
        if ($rememberme) {
            $signin_token = $user->username . $user->id . $user->last_login_time;
            cookie('uid', $user->id, 24 * 3600 * 7);
            cookie('signin_token', data_auth_sign($signin_token), 24 * 3600 * 7);
        }

        return $user->id;
    }
}
