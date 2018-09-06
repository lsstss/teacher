<?php
class liveBroadcast
{

/*
 *  @param 连接数据库
 *  @param 请确保打开mysqli扩展
 *  @param 建议PHP版本5.6+
 *  
 */
	public function __construct()
	{
		$mysql_conf = array(
		    'host'    => '127.0.0.1:3306', 
		    'db'      => 'test', 
		    'db_user' => 'root', 
		    'db_pwd'  => '0920', 
		    );
		$mysqli = @new mysqli($mysql_conf['host'], $mysql_conf['db_user'], $mysql_conf['db_pwd']);
		if ($mysqli->connect_errno) {
		    die("could not connect to the database:\n" . $mysqli->connect_error);
		}
		$mysqli->query("set names 'utf8';");
		$select_db = $mysqli->select_db($mysql_conf['db']);
		if (!$select_db) {
		    die("could not connect to the db:\n" .  $mysqli->error);
		}
	}


	public function createLiveSteam()
	{
		$method = 'POST';
		$space = 'zhibo0001';  //直播空间名
		$path = '/v2/hubs/'.$space.'/streams';
		$host = 'pili.qiniuapi.com';
		$contentType = 'application/json'; // HTTP/1.1
		$stream_name = '';
		$body = json_encode([
			'key' => $stream_name  //直播流名称
		]);
		$token = "$method $path\nHost: $host\nContent-Type: $contentType\n\n$body";
		$access_key = '';
		$secret_key = '';
		$ak = $access_key;
		$sk = $secret_key;
		require './Qiniu/Auth.php';
		require './src/Client.php';
		$qiniu = new \Qiniu\Auth($ak,$sk);
		$quan = 'Qiniu '.$qiniu->sign($token);
		$cli = new \GuzzleHttp\Client();
		//发送到七牛云平台创建直播流
		$res = $cli->request($method,$host.$path,[
			    'headers'=>[
			        'Authorization'=>$quan,
			        'Content-Type'=>'application/json',
			        'Accept-Encoding'=>'gzip',
			        'Content-Length'=>strlen($body),
			        'User-Agent'=>'pili-sdk-go/v2 go1.6 darwin/amd64',
			    	],
			    	'body'=>$body,
				]);
		return '直播流创建成功';
	}


	public function makePushFlow() 
	{
		$host = 'pili-publish.www.hanguophp.cn'; //直播空间绑定的 RTMP 推流域名
		$space = 'zhibo0001'; //直播空间名
		$stream_name = '';   //直播流名称
		$path = "/$space/$stream_name";
		$access_key = '';
		$secret_key = '';
		$ak = $access_key;
		$sk = $secret_key;
		require './Qiniu/Auth.php';
		$qiniu = new \Qiniu\Auth($ak,$sk);
		$quan = $qiniu->sign($path);
		return 'rtmp://'.$host.$path.'&token='.$quan;
	}

	public function makePulla()
	{
		/*
		 *格式：
		 *rtmp://<RTMPPlayDomain>/<Hub>/<StreamKey>
		 */
		$host = 'pili-publish.www.hanguophp.cn'; //直播空间绑定的 RTMP 推流域名
		$space = 'zhibo0001'; //直播空间名
		$stream_name = '';   //直播流名称
		return 'rtmp://'.$host.$space.$stream_name;

	}

}















