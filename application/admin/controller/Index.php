<?php
namespace app\admin\controller;

use think\View;
class Index
{
    public function index()
    {
       $view = new View();
        return $view->fetch('Index/index');
        // return json_encode('inde555x');
    }
}
