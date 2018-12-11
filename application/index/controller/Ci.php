<?php
namespace app\index\controller;

use think\Controller;
use think\View;
use think\Db;
use think\Session;
use think\Request;

class Ci extends controller
{
    public function index()
    {
	$output = null;
        exec(' php /root/git/zzz/git-cli.php',$output);
	print_r($output);
    }
}
