<?php
namespace app\index\controller;

use think\Controller;
use think\View;
use think\Db;
use think\Session;
use think\Request;

class Index extends Controller
{
    public function index()
    {   
    	$view = new View();
		return $view->fetch('index');
		// return json_encode('inde555x');
    }

    public function boke_load () {
        $boke = Db::table('tb_boke') -> limit(10) -> select();
        if(!$boke){
            $status = array('status' => 404, 'msg' => '暂无');
            return json_encode($status);
        }

        $status = array('status' => 200, 'msg' => 'success', 'data' => $boke);
        return json_encode($status);
    }

    public function vote_load () {
    	$vote = Db::table('tb_vote') -> where('open', 1) -> find();
    	if(!$vote){
            $status = array('status' => 404, 'msg' => '暂无', 'data' => '');
            return json_encode($status);
    	}

    	$count_data = Db::table('tb_user_vote') -> where('vote_id', $vote['id']) -> select();
		$count = count($count_data);

    	$count_data_1 = Db::table('tb_user_vote') -> where(array('vote_id' => $vote['id'], 'vote' => 1)) -> select();
		$count_1 = count($count_data_1);

    	$count_data_2 = Db::table('tb_user_vote') -> where(array('vote_id' => $vote['id'], 'vote' => 2)) -> select();
		$count_2 = count($count_data_2);

		$vote['vote_content'] = $vote['vote_content'];

    	$data = array('vote' => $vote, 'count' => $count, 'count_1' => $count_1, 'count_2' => $count_2);
        $status = array('status' => 200, 'msg' => 'success', 'data' => $data);
        return json_encode($status);
    }

    public function vote_user () {
    	$request = Request::instance();
    	$post = $request -> post();
    	if (!$post) {
	        $status = array('status' => 404, 'msg' => 'post is not exist');
	        return json_encode($status);
    	}
    	$vote_user = Db::table('tb_user_vote') -> where(array('user_id' => $post['user_id'], 'vote_id' => $post['vote_id'])) -> find();

    	if (!$vote_user) {
	        $status = array('status' => 404, 'msg' => 'dont vote');
	        return json_encode($status);
    	}

	    $status = array('status' => 200, 'msg' => 'seccess');
	    return json_encode($status);
    }

    public function check_login() {
    	$request = Request::instance();
    	$post = $request -> post();

    	if (!$post) {
	        $status = array('status' => 404, 'msg' => 'post is not exist');
	        return json_encode($status);
    	}

        $data = Db::table('tb_user')->where(array('username' => $post['username']))->find();

        if (!$data) {
            $status = array('status' => 404, 'msg' => '账户尚未注册！');
            return json_encode($status);
        }

        if (md5($post['password']) != $data['password']) {
            $status = array('status' => 404, 'msg' => '密码错误！');
            return json_encode($status);
        }

        $session_data = array('loginId' => $data['id'], 'nickname' => $data['nickname']);
        Session::set('session_data', $session_data);

        $status = array('status' => 200, 'msg' => '登录成功!');
    	return json_encode($status);
    }

    public function check_regist() {
        $request = Request::instance();
        $post = $request -> post();

        if (!$post) {
	        $status = array('status' => 404, 'msg' => 'post is not exist');
	        return json_encode($status);
        }

        $username = $post['username'];
        $password = md5($post['password']);
        $data = Db::table('tb_user')->where(array('username' => $username))->find();

        if ($data) {
            $status = array('status' => 404, 'msg' => '此账号已被注册!');
            return json_encode($status);
        }

        $insertData = array('username' => $username, 'password' => $password, 'nickname' => $username, 'create_time' => date("Y-m-d H:i:s"));
        Db::table('tb_user')->insert($insertData);
        $insertID = Db::name('tb_user') -> getLastInsID();

        if (!$insertID) {
            $status = array('status' => 404, 'msg' => '注册失败!请稍后再试');
            return json_encode($status);
        }

        $session_data = array('loginId' => $insertID, 'nickname' => $username);
        Session::set('session_data', $session_data);

        $status = array('status' => 200, 'msg' => '注册成功!');
        return json_encode($status);
    }

    public function login_out() {
        Session::delete('session_data');
        Session::clear();

        if (Session::get('session_data')) {
            $status = array('status' => 404, 'msg' => 'fail');
            return json_encode($status);
        }

        $status = array('status' => 200, 'msg' => 'seccess to loginOut');
        return json_encode($status);
    }

    public function to_vote() {
        $request = Request::instance();
        $post = $request -> post();

        if (!$post) {
	        $status = array('status' => 404, 'msg' => 'post is not exist');
	        return json_encode($status);
        }

        $user_id = $post['user_id'];
        $vote_id = $post['vote_id'];
        $vote = $post['vote'];

    	$vote_user = Db::table('tb_user_vote') -> where(array('user_id' => $post['user_id'], 'vote_id' => $post['vote_id'])) -> find();

        if ($vote_user) {
	        $status = array('status' => 404, 'msg' => '您已经投过票了，请刷新查看！');
	        return json_encode($status);
        }

        $insertData = array('user_id' => $user_id, 'vote_id' => $vote_id, 'vote' => $vote, 'create_time' => date("Y-m-d H:i:s"));
        Db::table('tb_user_vote') -> insert($insertData);
        $insertID = Db::name('tb_user_vote') -> getLastInsID();

        if (!$insertID) {
	        $status = array('status' => 404, 'msg' => '投票失败！请稍后再试');
	        return json_encode($status);
        }

        $status = array('status' => 200, 'msg' => '投票成功！');
        return json_encode($status);
    }
}
