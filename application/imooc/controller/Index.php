<?php
namespace app\imooc\controller;
use think\Session;

class Index
{
    public function index()
    {

        //获得参数 signature nonce token timestamp echostr
        $nonce     = $_GET['nonce'];
        $token     = 'meiquan520';
        $timestamp = $_GET['timestamp'];
       // $echostr   = $_GET['echostr'];
        $signature = $_GET['signature'];
        //形成数组，然后按字典序排序
        $array = array();
        $array = array($nonce, $timestamp, $token);
        sort($array);
        //拼接成字符串,sha1加密 ，然后与signature进行校验
        $str = sha1( implode( $array ) );

//        if( ($str  == $signature) && ($echostr) ){
//            //第一次接入weixin api接口的时候
//            echo  $echostr;
//            exit;
//        }
        if (!empty($_GET['echostr'])){
            $echostr   = $_GET['echostr'];
            if( $str  == $signature && $echostr ){
                //第一次接入weixin api接口的时候
                echo  $echostr;
                exit;
            }
        }
        else{
            $this->reponseMsg($str);
        }
    }

    //接收事件推送回复
    public function reponseMsg($str)
    {

            //获取微信推送过来的数据
        $postArr = file_get_contents("php://input");
            //2.处理消息类型，并设置回复类型和内容
            /*<xml>
    <ToUserName><![CDATA[toUser]]></ToUserName>
    <FromUserName><![CDATA[FromUser]]></FromUserName>
    <CreateTime>123456789</CreateTime>
    <MsgType><![CDATA[event]]></MsgType>
    <Event><![CDATA[subscribe]]></Event>
    </xml>*/
            //xml转换成对象格式
            $postObj = simplexml_load_string($postArr);
            if (strtolower($postObj->MsgType) == 'event') {
                //如果是关注 subscribe 事件
                if (strtolower($postObj->Event == 'subscribe')) {
                    //回复用户消息(纯文本格式)
                    $toUser = $postObj->FromUserName;
                    $fromUser = $postObj->ToUserName;
                    $time = time();
                    $msgType = 'text';
                    $content = '欢迎关注我们的微信公众账号';
                    $template = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[%s]]></MsgType><Content><![CDATA[%s]]></Content></xml>";
                    $info = sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
                    echo $info;
                    /*<xml>
                    <ToUserName><![CDATA[toUser]]></ToUserName>
                    <FromUserName><![CDATA[fromUser]]></FromUserName>
                    <CreateTime>12345678</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[你好]]></Content>
                    </xml>*/


                }

                //点击菜单拉取消息时的事件推送
                if (strtolower($postObj->Event) == 'click'){
                    if (strtolower($postObj->EventKey) == 'item1'){
                        model('Index')->responseText($postObj,'您点击了事件一');
                    }
                    if (strtolower($postObj->EventKey) == 'item2'){
                        model('Index')->responseText($postObj,'您点击了事件二');
                    }
                }
            }






		//回复图文
       /* <xml><ToUserName>< ![CDATA[toUser] ]></ToUserName><FromUserName>< ![CDATA[fromUser] ]></FromUserName><CreateTime>12345678</CreateTime><MsgType>< ![CDATA[news] ]></MsgType><ArticleCount>1</ArticleCount><Articles><item><Title>< ![CDATA[title1] ]></Title> <Description>< ![CDATA[description1] ]></Description><PicUrl>< ![CDATA[picurl] ]></PicUrl><Url>< ![CDATA[url] ]></Url></item></Articles></xml>*/
        if (strtolower($postObj->MsgType=='text')){




            switch( trim($postObj->Content) ){
                case 1:
                    $content = '您输入的数字是1';
                    break;
                case 2:
                    $content = '您输入的数字是2';
                    break;
                case 3:
                    $content = '您输入的数字是3';
                    break;
                case 4:
                    $content = "<a href='http://www.imooc.com'>慕课</a>";
                    break;
                case '英文':
                    $content = 'imooc is ok';
                    break;
                case'tuwen':
                    $arr = array(
                    array(
                        'title'=>'hao123',
                        'description'=>"hao123 is very cool",
                        'picUrl'=>'https://www.baidu.com/img/bdlogo.png',
                        'url'=>'http://www.hao123.com',
                    )
            );
            model('Index')->responseArtText($postObj,$arr);
                   break;
                case '思欣':
                    $content='嘿，思欣小伙伴，您好，欢迎关注肥仔泉公众号';
                    break;
                default:
                    $url="https://www.apiopen.top/weatherApi?city=".urlencode($postObj->Content);
                    $obj=$this->do_curl($url,'get','json');
                    if ($obj['code']=='200') {
                        $content = '地点：' . $obj['data']['city'] . "\n";
                        $content .= '今日天气：' . $obj['data']['forecast'][0]['type'] . "\n";
                        $content .= '今日天气最高温度：' . $obj['data']['forecast'][0]['high'] . "\n";
                        $content .= '今日天气最低温度：' . $obj['data']['forecast'][0]['low'] . "\n";
                    }else{
                        $content='暂无该关键字回复，请输入数字或则地方查询天气预报';
                    }
            }


            model('Index')->responseText($postObj,$content);

        }

        }

        //创建自定义菜单
    public function definedItem(){
        header('content-type:text/html;charset=utf-8');
       $access_token=$this->getAccessToken();
        $url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;

        $arr=array(
            'button'=>array(
                array(
                    "type"=>"click",
          "name"=>urlencode('点击一'),
          "key"=>"item1"
                ),
                array(
                    'name'=>urlencode('点击二'),
                    'sub_button'=>array(
                        array(
                           "type"=>"view",
                           "name"=>urlencode('搜索'),
                           "url"=>"http://www.soso.com/"
                        ),
                        array(
                            "type"=>"click",
                           "name"=>urlencode('赞一下我们'),
                           "key"=>"item2"
                        )
                    )

                ),
                array(
                    "type"=>"view",
                    "name"=>urlencode('百度'),
                    "url"=>"http://www.baidu.com/"
                ),
            )
        );
        $arr=urldecode(json_encode($arr));
        $res=$this->do_curl($url,'post','json',$arr);
        return var_dump($res);
    }


        //或则access_token
    public function getAccessToken(){
        //判断SESSION是否存在
        if ( Session::has('access_token') &&  Session::get('expire_time')>time()){
            //已经存在并且没有过期
           return Session::get('access_token');
        }else{
            $appid='wxe4e54d0da1768428';
            $secret='358b620b44d5309d1ce33065ba11565c';
            $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
           $res=$this->do_curl($url,'get','json');
            $access_token=$res['access_token'];
            Session::set('access_token',$access_token);
            Session::set('expire_time',time()+7000);
           return  $access_token;
        }
    }


        public function  do_curl($url,$type='get',$res='json',$arr=''){

            //1.初始话
            $ch=curl_init();
             //2.设置参数
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

            if ($type=='post'){
                curl_setopt($ch,CURLOPT_POST,1);
                curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
            }

            //3.采集数据
            $output=curl_exec($ch);


        //判断返回的格式是否是JSON
            if ($res=='json'){
                //是否出错
                if (curl_errno($ch)){
                    return curl_error($ch);
                }else {
                    return json_decode($output,true);
                }
            }

            //4.关闭
            curl_close($ch);

        }
        public  function text(){
         Session::clear();
        }

}
