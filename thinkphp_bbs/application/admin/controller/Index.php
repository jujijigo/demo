<?php

namespace app\admin\controller;

use app\admin\model\User;
use app\admin\model\Topic;
use app\admin\model\Reply;
use app\admin\model\Praise;

class Index extends \think\Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->request->action() !== 'login') {
            $user = session('adminUser');
            if (!$user || !$user->is_admin) {
                return $this->error('您未登陆或者不是管理员','index/login');
            }
        }
    }

    public function login()
    {
        if (request()->isPost()) {
            $login = input('post.username');
            $password = input('post.password');
            $cond = array();
            $cond['name|email'] = $login;
            $cond['password'] = md5($password);
            $user = User::get($cond);
            if ($user && $user->is_admin) {
                session('adminUser', $user);
                return $this->success('登陆成功！', 'index/index');
            }
            return $this->error('登陆失败或者用户不是管理员！');
        }
        echo $this->fetch('login');
    }

    public function index()
    {
        $user = session('adminUser');
        $this->assign([
            'user' => $user,
            'active' => 'index',
            'usersCount' => User::count(),
            'praisesCount' => Praise::count(),
            'repliesCount' => Reply::count(),
            'topicsCount' => Topic::count(),
            'username'=>$user->name,
            'avatar'=>$user->avatar,
        ]);
        echo $this->fetch('index');
    }

    public function users()
    {
        $user = session('adminUser');
        $this->assign([
            'user' => $user,
            'active' => 'users',
            'users' => User::withCount(['topics'])->where(['is_delete' => 0])->select(),
        ]);
        echo $this->fetch('user_manage');
    }

    public function delUser()
    {
        $userId = input('get.userId');
        $user = User::find($userId);
        $user->is_delete = 1;
        $user->save();
        $this->success('删除成功！', 'index/users');
    }

    public function topics()
    {
        $user = session('adminUser');
        $this->assign([
            'user' => $user,
            'active' => 'topics',
            'topics' => Topic::where(['is_delete' => 0])->select(),
        ]);
        echo $this->fetch('topic_manage');
    }

    public function delTopic()
    {
        $topicId = input('get.topicId');
        $topic = Topic::find($topicId);
        $topic->is_delete = 1;
        $topic->save();
        $this->success('删除成功！', 'index/topics');
    }

    public function logout()
    {
        session('adminUser', null);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        $this->success('退出成功', url("index/login"));
    }
}
