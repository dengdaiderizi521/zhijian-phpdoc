<?php
/**
 * DEBUG文件，此处可以自定义调试请求逻辑
 */

include_once __DIR__.'/config.php';
echo send_curl($config['debug_form_url'], $_POST);

/**
 * 发送请求
 * @param string $url 请求的参数
 * @param array $postFields 如果传入参数表示POST请求
 * @throws Exception
 * @return string
 * 
 */
function send_curl($url, $postFields = null)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FAILONERROR, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 120);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	//https 请求
	if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	}

	if (is_array($postFields) && 0 < count($postFields))
	{
		$postBodyString = "";
		$postMultipart = false;
		$dimcount = 0;
		foreach ($postFields as $k => $v)
		{
		    //判断是否是多维数组
		    if(is_array($v)){
		        $dimcount = 1;
		        break;
		    }
			if("@" != substr($v, 0, 1))//判断是不是文件上传
			{
				$postBodyString .= "$k=" . urlencode($v) . "&"; 
			}
			else//文件上传用multipart/form-data，否则用www-form-urlencoded
			{
				$postMultipart = true;
			}
		}
		unset($k, $v);
		curl_setopt($ch, CURLOPT_POST, true);
		if ($postMultipart)
		{
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		}
		else
		{
		    //判断是否是多维数组，如果是多维数组就把数组转换成url的query字符串
		    if($dimcount > 0){
		        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
		    }else{
			    curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString,0,-1));
		    }
		}
		
	}
	$reponse = curl_exec($ch);
	
	curl_close($ch);
	return $reponse;
}