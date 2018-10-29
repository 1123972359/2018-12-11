<?php
namespace app\admin\controller;
use think\View;
class Login
{
    public function index()
    {
        $view = new View();
        return $view->fetch('index');
    }
}
