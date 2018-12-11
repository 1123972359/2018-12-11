<?php
namespace app\imooc\model;
use think\Model;
/*
 * 微信公众JDK
 * */
 class Index extends Model{

     //被动回复--文字回复
     public function responseText($postObj,$content){

         $template = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[%s]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
//注意模板中的中括号 不能少 也不能多
         $fromUser = $postObj->ToUserName;
         $toUser   = $postObj->FromUserName;
         $time     = time();
         // $content  = '18723180099';
         $msgType  = 'text';
         echo sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
     }

     //被动回复--图文回复
     public function responseArtText($postObj,$arr){
         $toUser = $postObj->FromUserName;
         $fromUser = $postObj->ToUserName;

         $template = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<ArticleCount>".count($arr)."</ArticleCount>
						<Articles>";
         foreach($arr as $k=>$v){
             $template .="<item>
							<Title><![CDATA[".$v['title']."]]></Title> 
							<Description><![CDATA[".$v['description']."]]></Description>
							<PicUrl><![CDATA[".$v['picUrl']."]]></PicUrl>
							<Url><![CDATA[".$v['url']."]]></Url>
							</item>";
         }

         $template .="</Articles>
						</xml> ";
         echo sprintf($template, $toUser, $fromUser, time(), 'news');
     }
 }
