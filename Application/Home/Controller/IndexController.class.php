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
        file_put_contents("./Public/reponseMsg.txt",$postArr);
        //2.处理消息类型，并设置回复类型和内容
//        <xml>
//        <ToUserName>< ![CDATA[toUser] ]></ToUserName>
//        <FromUserName>< ![CDATA[FromUser] ]></FromUserName>
//        <CreateTime>123456789</CreateTime>
//         <MsgType>< ![CDATA[event] ]></MsgType>
//        <Event>< ![CDATA[subscribe] ]></Event>
//        </xml>

        $postObj = simplexml_load_string($postArr);
        file_put_contents("./Public/FromUserName.txt",$postObj->FromUserName);
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
                'Url'=>'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx14e494d69e7d2980&redirect_uri=http://wangmutian.gotoip11.com/weixin/index.php/Home/Index/getopenid&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect'
            ),
            array(
                "title"=>'imooc',
                'description'=>'imooc is very cool',
                'picUrl'=>'http://t2.hddhhn.com/uploads/tu/201612/98/st93.png',
                'Url'=>'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx14e494d69e7d2980&redirect_uri=http://wangmutian.gotoip11.com/weixin/index.php/Home/Index/getopenid&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect'
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


//    function http_curl($url){
////        $url = 'http://www.baidu.com';
//        //1.第一部初始化curl
//        $ch  = curl_init();
//        //2.设置参数
//        curl_setopt($ch,CURLOPT_URL,$url);
//        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
//        //3.采集
//        $output = curl_exec($ch);
//        //4.关闭
//        curl_close($ch);
//        var_dump($output);
//    }


    function http_curl($url,$type='get',$res = 'json',$arr){

        //1.第一部初始化curl
        $ch  = curl_init();
        //2.设置参数
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        if($type == 'post'){
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
        }
        //3.采集

        $output = curl_exec($ch);
        //4.关闭
        curl_close($ch);
        if($res == 'json'){
            return json_decode($output,true);
        }
        var_dump($output);
    }

    public function getWXAccessToken(){
        $appid = "wxaa8996ee722227bc";
        $appSecret = "5b45e60d9ff5625818eaf9b928a42959";
        //https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET



        if($_SESSION['access_token'] && $_SESSION['expires_time']>time()){
            //access_token 在session 并没有过期
            return $_SESSION['access_token'];
        }else{
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appSecret";
            $res = file_get_contents($url);
            $arr = json_decode($res, true);
            $_SESSION['access_token'] = $arr['access_token'];
            $_SESSION['expires_time'] = time() + $arr['expires_in'] - 200;
            return $arr['access_token'];
        }

    }

    function getWXServerIp(){
        $accessToken = $this->getWXAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=".$accessToken;
        $res = file_get_contents($url);
        var_dump($res);
    }


    public function curl(){
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, 'http://www.baidu.com');
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        //设置post数据
        $post_data = array(
            "username" => "coder",
            "password" => "12345"
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        var_dump($data);
    }

    public function definedItem(){
        $accessToken = $this->getWXAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$accessToken;
        echo $url;
        $postArr = array(
            'button'=>array(
                array(
                    'type'=>'click',
                    'name'=> 'xxxx',
                    "key"=>"item1"
                )

            )
        );
        echo '<br/>';
        $postJson = json_encode($postArr);

        echo $postJson;

        $res = $this->http_curl($url,'post','json',$postJson);
        var_dump($res);
    }

    //群发接口
    function sendMsgAll(){
        //1.获取全局access_token
        $accessToken = $this->getWXAccessToken();
        $url = "http://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=".$accessToken;
        //2.组装群发接口数据 array
        //openid :oBemTwt2xBys6X_mvXNP6SZIXjkY
        /*
        {
            "touser":"OPENID",
            "text":{
                   "content":"CONTENT"
                   },
            "msgtype":"text"
        }
        */
        $array=array(
            'touser'=>"oe9JV00rBhuecekHopO7OtJUKuEI", //微信用户的openid
            'text'=>array('content'=>"imooc is very happy"),
            'msgtype'=>"text"
        );
        //3.将数组转成json

        $postJson = json_encode($array);

//        $res = $this->http_curl('http://www.baidu.com','post','json',$postJson);
//        var_dump($res);


        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        //设置post数据

        curl_setopt($curl, CURLOPT_POSTFIELDS, $postJson);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
//        print_r($data);
        var_dump(curl_errno($curl));
        var_dump($data);
    }


    public function getopenid(){
        $code = $_GET['code'];//获取code
        echo $code;
    }


    //获取用户的openid
    public function getBaseInfo(){
        //1.获取code http://www.texunkeji.net/weixin/index.php/Home/index/weixinjieru
        //https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect 若提示“该链接无法访问”，请检查参数是否填写错误，是否拥有scope参数对应的授权作用域权限。
        $redirect_uri = urlencode("http://www.texunkeji.net/weixin/index.php/Home/index/getUserOpenId");
        $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxaa8996ee722227bc&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
        header("location:".$url);
        //2.获取网页授权的access_token
        //3.拉取用户的openid

    }

    public function getUserOpenId(){
        var_dump($_GET);
        echo "<hr>";
        $code = $_GET['code'];

        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxaa8996ee722227bc&secret=5b45e60d9ff5625818eaf9b928a42959&code=".$code."&grant_type=authorization_code";
        $res = file_get_contents($url);
        var_dump($res);
    }



}