<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
    }
    /**
     * 微信文档
     * https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140543
     *
     *
     * AppID：wx14e494d69e7d2980
     * AppSecret：4379d39eaf51ca9d03b2f77991db901b
     */
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
        file_put_contents("./Public/post.txt",$postObj->Content);
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

        //回复用户消息
        $toUser = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $time = time();
//        $MsgType = 'text';
//        $Content = $postObj->Content;
//        $template = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[%s]]></MsgType><Content><![CDATA[%s]]></Content></xml>";
//        $info = sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
//        echo $info;


        //单图文消息



        $arr = array(
            array(
                "title"=>'imooc',
                'description'=>'imooc is very cool',
                'picUrl'=>'http://t2.hddhhn.com/uploads/tu/201612/98/st93.png',
                'Url'=>'http://www.badiu.com'
            ),
            array(
                "title"=>'imooc',
                'description'=>'imooc is very cool',
                'picUrl'=>'http://t2.hddhhn.com/uploads/tu/201612/98/st93.png',
                'Url'=>'http://www.badiu.com'
            ),

        );
        $template = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <ArticleCount>".count($arr)."</ArticleCount>                        
                        <Articles>";
        foreach ($arr  as $k=>$v){
            $template .= "<item>
                            <Title><![CDATA[".$v['title']."]]></Title>
                            <Description><![CDATA[".$v['description']."]]></Description>
                            <PicUrl><![CDATA[".$v['picUrl']."]]></PicUrl>
                            <Url><![CDATA[".$v['Url']."]]></Url>
                            </item>";
        }

        $template .= "</Articles>
                        </xml>";

        echo sprintf($template,$toUser,$fromUser,$time,'news');


    }


    function http_curl($url){
//        $url = 'http://www.baidu.com';
        //1.第一部初始化curl
        $ch  = curl_init();
        //2.设置参数
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //3.采集
        $output = curl_exec($ch);
        //4.关闭
        curl_close($ch);
        var_dump($output);
    }

    public function getWXAccessToken(){
        $appid = "wx14e494d69e7d2980";
        $appSecret = "4379d39eaf51ca9d03b2f77991db901b";
        //https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appSecret";
        $res = file_get_contents($url);

        $arr = json_decode($res, true);
        return $arr['access_token'];

    }

    function getWXServerIp(){
        $accessToken = $this->getWXAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=".$accessToken;
        $res = file_get_contents($url);
        var_dump($res);
    }

}