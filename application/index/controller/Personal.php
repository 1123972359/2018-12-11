<?php
namespace app\index\controller;

use think\Controller;
use think\View;
use think\Db;
use think\Session;
use think\Request;

class Personal extends controller
{
	public function personal_index(){
		$view = new View();
		return $view -> fetch('personal');
	}

	public function vote_lists() {
		$request = Request::instance();
		$post = $request -> post();

    	if (!$post) {
	        $status = array('status' => 404, 'msg' => 'post is not exist');
	        return json_encode($status);
    	}

    	$votes = Db::table('tb_user_vote') -> where(array('user_id' => $post['user_id'])) -> select();

    	if (!$votes) {
	        $status = array('status' => 404, 'msg' => '您还没有参与任何投票!');
	        return json_encode($status);
    	}

    	$vote_datas = [];

        $num = 4;

        if ($post['index'] == 1 || $post['index'] == 0) {
            $big_num = $num;
        } else {
            $big_num = $post['index'] * $num;
        }

        $min_num = $big_num - $num;

    	foreach (array_reverse($votes) as $key => $vote) {
            if ($key < $big_num && $key >= $min_num) {
                $v = Db::table('tb_vote') -> where('id', $vote['vote_id']) -> find();

                $v['vote'] = '';

                if ($vote['vote'] == 1) {
                    $v['vote'] =  $v['vote_text1'];
                } else if ($vote['vote'] == 2) {
                    $v['vote'] =  $v['vote_text2'];
                }

                array_push($vote_datas, $v);
            } 
    	}

        // $vote_datas = array_reverse($vote_datas);

        $status = array('status' => 200, 'msg' => 'seccess', 'data' => $vote_datas, 'vote_lists_length' => count($votes), 'page_num' => $num);

        return json_encode($status);
	}

    public function nickname_modiify () {
        $request = Request::instance();
        $post = $request -> post();

        if(!$post){
            $status = array('status' => 404, 'msg' => 'post is not exist');
            return json_encode($status);
        }

        $update = Db::table('tb_user') -> where(array('id' => $post['user_id'])) -> setField(array('nickname' => $post['nickname']));

        if(!$update){
            $status = array('status' => 404, 'msg' => '修改失败!');
            return json_encode($status);
        }

        $status = array('status' => 200, 'msg' => '修改成功!');
        Session::set('session_data.nickname', $post['nickname']);
        return json_encode($status);
    }
}