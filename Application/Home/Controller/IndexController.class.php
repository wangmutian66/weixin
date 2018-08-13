<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
    }

    //微信接入
    public function weixinjieru(){
        $nonce = $_GET['nonce'];
        $token = 'weixin';
        $timestamp = $_GET['timestamp'];
        $echostr = $_GET['echostr'];
        $signature = $_GET['signature'];
        $array = array($timestamp,$nonce,$token);
        sort($array);
        $tmpstr = implode('', $array);
        $tmpstr = sha1($tmpstr);
        //$echostr
        if($tmpstr == $signature && $echostr){
            //第一次接入weixin api的时候
            echo $_GET['echostr'];
            exit();
        }else{
            $this->reponseMsg();
        }
    }

    //接收事件推送并回复
    public function reponseMsg(){
        //1.获取到微信推送过来的post数据（xml）
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        //2.处理消息类型，并设置回复类型和内容
//        <xml>
//        <ToUserName>< ![CDATA[toUser] ]></ToUserName>
//        <FromUserName>< ![CDATA[FromUser] ]></FromUserName>
//        <CreateTime>123456789</CreateTime>
//         <MsgType>< ![CDATA[event] ]></MsgType>
//        <Event>< ![CDATA[subscribe] ]></Event>
//        </xml>

        $postObj = simplexml_load_string($postArr);
//        $postObj->ToUserName = '';
//        $postObj->FromUserName = '';
//        $postObj->CreateTime = '';
//        $postObj->MsgType = '';
//        $postObj->Event = '';

        //判断该数据包是否是订阅的事件推送
        if(strtolower($postObj->MsgType) == 'event'){

            //如果是关注subscribe事件
            if(strtolower($postObj->Event) == 'subscribe'){
                //回复用户消息
                $toUser = $postObj->FromUserName;
                $fromUser = $postObj->ToUserName;
                $time = time();
                $MsgType = 'text';
                $Content = '欢迎关注我们的微信公众账号';
                $template = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[%s]]></MsgType><Content><![CDATA[%s]]></Content></xml>";

                $info = sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
                echo $info;

            }
        }


    }


   




}