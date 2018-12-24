<?php
namespace app\index\controller;

use think\Controller;
use think\View;
use think\Db;
use think\Request;

class BokeEditor extends controller
{
	public function index(){
		$view = new View();
		return $view -> fetch('bokeEditor');
	}

	public function toBokeInfo(){
		$view = new View();

		$request = Request::instance() -> get();

		if(!$request){
			$msg = array('err_code' => 404, 'err_msg' => 'request fails');
			dump($msg);
			exit();
		}

		if(!$request['id']){
			$msg = array('err_code' => 405, 'err_msg' => 'id not exists');
			dump($msg);
			exit();
		}

		$isComeFromPerson = $request['isComeFromPerson'];

		$data = Db::table('tb_boke') -> where(array('id' => $request['id'])) -> find();

		return $view -> fetch('bokeInfo', ['id' => $request['id'], 'isComeFromPerson' => $isComeFromPerson, 'data' => $data]);
	}

	public function bokeInfo(){
		$request = Request::instance() -> get();

		$data = Db::table('tb_boke') -> where(array('id' => $request['id'])) -> find();

		if(!$data){
			$msg = array('err_code' => 406, 'err_msg' => 'data not exists');
			return json_encode($msg);
		}

		$user = Db::table('tb_user') -> where(array('id' => $data['uid'])) -> field('nickname') -> find();

		$data['nickname'] = $user['nickname'];

		$msg = array('err_code' => 200, 'err_msg' => 'success', 'data' => $data);
		return json_encode($msg);
	}

	public function upload(){
	    $file = request()->file('image');
	    if($file){
	        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
	        if($info){
	            // 输出 jpg
	            // echo $info->getExtension();
	            // // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
	            // echo $info->getSaveName();
	            // // 输出 42a79759f284b767dfcb2a0197904287.jpg
	            // echo $info->getFilename(); 
				$data["errno"] = 0;
		    	$data["data"] = ['http://www.linbinbin.top' . DS . 'uploads' . DS .$info->getSaveName()];
	        }else{
		    	$data["errno"] = 1;
		    	$data["data"] = ['upload fails'];
	    	}
	    }else{
	    	$data["errno"] = 2;
		    $data["data"] = ['no files'];
	    }
	    exit(json_encode($data));
	}

	public function saveBoke(){
		$request = Request::instance() -> post();

		if($request){

			$insertData = array('uid' => $request['uid'], 'title' => $request['title'], 'content' => $request['content'], 'create_time' => date('Y-m-d H:i:s'), 'is_draft' => $request['is_draft']);

			Db::table('tb_boke') -> insert($insertData);

			$insertID = Db::table('tb_boke') -> getLastInsID();

			if($insertID){
				$data = array('err_code' => 200, 'err_msg' => 'success');
			}else{
				$data = array('err_code' => 500, 'err_msg' => 'save fails');
			}

			exit(json_encode($data));
		}else{
			$data = array('err_code' => 404, 'err_msg' => 'no post');
		}
		exit(json_encode($data));
	}

}