<?php

namespace src\controller;

use src\Controller;
use src\Db;

class Wxapi extends Controller{

    public function start($request,$response){

        define('TOKEN', 'php');

        if (empty($_GET['echostr']))
        {
            $this->responseMsg();
        }else{
            //首次：验证服务器地址有效性
            try{

                $this->checkSignature();

            }catch (\Exception $e){

            }

        }

    }

    public function responseMsg()
    {

        //相当于$_POST
//        $postStr = file_get_contents('php://input');
        $postStr =  $this->request->rawContent();

        //判断是否有数据
        if (!empty($postStr))
        {
            //XML数据安全过滤
            libxml_disable_entity_loader(true);
            //将腾讯服务器发送的XML文档转化为对象
            //对象：属性-XML标记名，值-XML标记值
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

            //获取内容
            $fromUsername = $postObj->FromUserName;   //发送者唯一标识 （A）
            $toUsername = $postObj->ToUserName;       //接收者唯一标识 （B）

//            $newsDatas[0]['title']='💗其实我很快乐';
//            $newsDatas[0]['desc']='11';
//            $newsDatas[0]['img']='http://yaayad.cn/erp/public/upload/carte_img/1550126698.png';
//            $newsDatas[0]['url']='https://mp.weixin.qq.com/s/uCJsLFYecWnPMxoSeOGuTQ';
//            $newsDatas[1]['title']='💙怀念无话不说的岁月';
//            $newsDatas[1]['desc']='22';
//            $newsDatas[1]['img']='http://yaayad.cn/erp/public/upload/carte_img/1550126698.png';
//            $newsDatas[1]['url']='https://mp.weixin.qq.com/s/uCJsLFYecWnPMxoSeOGuTQ';
//            $newsDatas[2]['title']='转身的那一刹那';
//            $newsDatas[2]['desc']='33';
//            $newsDatas[2]['img']='http://yaayad.cn/erp/public/upload/carte_img/1550126698.png';
//            $newsDatas[2]['url']='https://mp.weixin.qq.com/s/uCJsLFYecWnPMxoSeOGuTQ';



            if($postObj->Event=='subscribe'){
                 $this->sendText($fromUsername,  $toUsername, "新用户，参数".$postObj->EventKey);
//                $this->sendNews($fromUsername, $toUsername, $newsDatas);
            }
            if($postObj->Event=='SCAN'){
                 $this->sendText($fromUsername,  $toUsername, "老用户，参数".$postObj->EventKey);
//                $this->sendNews($fromUsername, $toUsername, $newsDatas);
            }

            if($postObj->MsgType=='voice'){

                $this->sendText($fromUsername, $toUsername, $postObj->Recognition);
            }else if($postObj->MsgType=="event"){

                if($postObj->Event=='CLICK'){
                    if($postObj->EventKey=="V1001_TODAY_MUSIC"){
                        $this->sendMusic($fromUsername,$toUsername, $MusicUrl, $MusicUrl, $ThumbMediaId);
                    }
                }else{
                    $this->sendText($fromUsername,  $toUsername, $this->talk($postObj->Content));
                }



            }else{
                $this->sendText($fromUsername,  $toUsername, $this->talk($postObj->Content));
            }




        } else {
            echo "";
            exit;
        }
    }

    public function checkSignature()
    {
        $echoStr = $this->request->get["echostr"];

        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new \Exception('TOKEN is not defined!');
        }

        $signature = $this->request->get["signature"];
        $timestamp = $this->request->get["timestamp"];
        $nonce = $this->request->get["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            $this->response->end($echoStr);

        }else{
//            return false;
        }
    }

    /*
     * 响应文本消息
     * @param string $fromUsername 发送者标识
     * @param string $toUsername   接受者标识
     * @param string $content      响应文本内容
    */
    public function sendText($fromUsername,  $toUsername, $content)
    {
        $xmlData = '<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[text]]></MsgType>
			<Content><![CDATA[%s]]></Content>
		</xml>';
        $this->response->end( sprintf($xmlData, $fromUsername, $toUsername,  time(), $content));
    }

    /*
     * 响应文本消息
     * @param string $fromUsername 发送者标识
     * @param string $toUsername   接受者标识
     * @param array  $newsData     图片数据，格式（  [  [title, desc, img, url], ...., [] ]  ）
    */
    public function sendNews($fromUsername,  $toUsername, $newsDatas)
    {
        $xmlData = '<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[news]]></MsgType>
			<ArticleCount>'.count($newsDatas).'</ArticleCount>
			<Articles>';

        foreach ($newsDatas as $newsData)
        {
            $xmlData .= "<item>
					<Title><![CDATA[{$newsData['title']}]]></Title>
					<Description><![CDATA[{$newsData['desc']}]]></Description>
					<PicUrl><![CDATA[{$newsData['img']}]]></PicUrl>
					<Url><![CDATA[{$newsData['url']}]]></Url>
				</item>";
        }

        $xmlData .= '</Articles>
			</xml>';
        $this->response->end( sprintf($xmlData, $fromUsername, $toUsername, time()) );

    }

    /*
     * 响应图片消息
     * @param string $fromUsername 发送者标识
     * @param string $toUsername   接受者标识
     * @param string $MediaId      媒体ID
    */
    public function sendImage($fromUsername,  $toUsername, $MediaId = 'f5KPevMR0ZEEfUkNAPa8i0ufPfZPiVtapTCtA9ZehmxHL5AA0WzgeEaHSigNaYQG')
    {
        $xmlData = '<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[image]]></MsgType>
			<Image>
				<MediaId><![CDATA[%s]]></MediaId>
			</Image>
		</xml>';
        $this->response->end( sprintf($xmlData, $fromUsername, $toUsername,  time(), $MediaId));

    }

    /*
     * 响应视频消息
     * @param string $fromUsername 发送者标识
     * @param string $toUsername   接受者标识
     * @param string $MediaId      媒体ID
    */
    public function sendVideo($fromUsername,  $toUsername, $MediaId = 'ymcb1KRToNseEibAEPn2T0OI2WwmIh53gCvsbtTGu_YItOAspeNV9LWIHz7XlxAZ')
    {
        $xmlData = '<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[video]]></MsgType>
			<Video>
				<MediaId><![CDATA[%s]]></MediaId>
				<Title><![CDATA[%s]]></Title>
				<Description><![CDATA[%s]]></Description>
			</Video>
		</xml>';
        $this->response->end( sprintf($xmlData, $fromUsername, $toUsername,  time(), $MediaId, '这是视频标题', '视频描述'));

    }

    /*
     * 响应音乐消息
     * @param string $fromUsername    发送者标识
     * @param string $toUsername      接受者标识
     * @param string $MusicUrl        音乐地址
     * @param string $HQMusicUrl      音乐地址（高清）
     * @param string $MediaId         媒体ID
    */
    public function sendMusic($fromUsername,  $toUsername, $MusicUrl, $HQMusicUrl, $ThumbMediaId = 'CZoX8caHprpGZDbhjHi2295YuCFHhHZ2RZ3yen6hulogh4XwZAIe_96mZOYrAfpu')
    {
        $xmlData = '<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[music]]></MsgType>
			<Music>
				<Title><![CDATA[音乐大放送]]></Title>
				<Description><![CDATA[好听的音乐]]></Description>
				<MusicUrl><![CDATA[%s]]></MusicUrl>
				<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
				<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
			</Music>
		</xml>';
        $this->response->end( sprintf($xmlData, $fromUsername, $toUsername,  time(), $MusicUrl, $HQMusicUrl, $ThumbMediaId) );
    }
    public function talk($content)
    {

        // $api = 'http://www.tuling123.com/openapi/api';
        // $postData = array(
        //    'key'=>'4894d3f06c844c1693562201987b7ef9',
        //    'info'=> isset($content) ? $content : '你好',
        //    'userid'=> 1
        // );

        // $postData = array(
        // 'key'=>'4894d3f06c844c1693562201987b7ef9',
        // 'info'=>'你好',
        // 'userid'=> 1
        // );
        $api = 'http://openapi.tuling123.com/openapi/api/v2';
        $postData='{
            "reqType":0,
            "perception": {
                "inputText": {
                    "text": "'.$content.'"
                }
            },
            "userInfo": {
                "apiKey": "4894d3f06c844c1693562201987b7ef9",
                "userId": "1"
            }
            }';

        $rs = json_decode($this->httpRequest($api, $postData),true);

        //  $rs =$this->httpRequest($api, json_encode($postData), true);

        return $rs['results'][0]['values']['text'];
    }



    /*
      * curl请求
      * @param string $api    请求地址不带 httpL://
      * @param string $postData      post数据
      * @param string $port        请求端口
     */
    public function httpRequest($api, $postData = array(),$port=80)
    {
            $host = substr($api,0,strpos('/',$api)-1);
            $link = substr($api,strpos('/',$api));

            $cli = new \Swoole\Coroutine\Http\Client($host, $port);
            $cli->setHeaders([
                'Host' => $host,
                "User-Agent" => 'Chrome/49.0.2587.3',
                'Accept' => 'text/html,application/xhtml+xml,application/xml',
                'Accept-Encoding' => 'gzip',
            ]);
            $cli->set([ 'timeout' => 1]);
            if(empty($postData)){
                $cli->get($link);
            }else {
                $cli->post($link, $postData);
            }
            $result = $cli->getBody();
            $cli->close();
            return $result;

    }





}