<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/20/020
 * Time: 23:07
 */

namespace app\Utility;


class Response extends \think\Response
{
    private $statusCode = 200;
    private $reasonPhrase = 'OK';


    public function getInstance()
    {
        return $this;
    }

    public function getStatusCode()
    {
        // TODO: Implement getStatusCode() method.
        return $this->statusCode;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        // TODO: Implement withStatus() method.
        if($code === $this->statusCode){
            return $this;
        }else{
            $this->statusCode = $code;
            if(empty($reasonPhrase)){
                $this->reasonPhrase = Status::getReasonPhrase($this->statusCode);
            }else{
                $this->reasonPhrase = $reasonPhrase;
            }
            return $this;
        }
    }

    public function getReasonPhrase()
    {
        // TODO: Implement getReasonPhrase() method.
        return $this->reasonPhrase;
    }

}