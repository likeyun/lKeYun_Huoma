<?php

    /**
     * 状态码说明
     * 200 成功
     * 201 未登录
     * 202 失败
     * 203 空值
     * 204 无结果
     */

	// 页面编码
	header("Content-type:application/json");
	
	// 判断登录状态
    session_start();
    if(isset($_SESSION["yinliubao"])){
        
        // 已登录
        $jw_id = trim($_GET['jw_id']);
        
        // 过滤参数
        if(empty($jw_id) || !isset($jw_id)){
            
            // 非法请求
            $result = array(
			    'code' => 203,
                'msg' => '非法请求'
		    );
        }else{
            
            // 数据库配置
        	include '../../../../Db.php';
        
        	// 实例化类
        	$db = new DB_API($config);
        	
            // 获取详情
            $getJwInfo = $db->set_table('ylb_jumpWX')->find(['jw_id'=>$jw_id]);
            
            // 返回数据
            if($getJwInfo){
                
                // 对象储存域名
                $jw_dxccym = json_decode(json_encode($getJwInfo))->jw_dxccym;
                
                // 根据域名决定生成的longUrl格式
                if(strpos($jw_dxccym,'qcloud.la') == TRUE){ 
                    
                    // 微信云托管
                    $longUrl = $jw_dxccym . '/index.html?jwid=' . $jw_id;
                    
                }else {
                    
                    // 其他
                    // 阿里云OSS、抖音云对象存储请上传名为index.html的文件
                    $longUrl = $jw_dxccym . '/?jwid=' . $jw_id;
                }
                
                // token
                $jw_token = json_decode(json_encode($getJwInfo))->jw_token;
                
                // 时间戳
                $timeNum = time();
                
                // 有结果
                $result = array(
        		    'code' => 200,
        		    'msg' => '获取成功',
        		    'longUrl' => $longUrl . '&t=' . $timeNum . '&f=url',
        		    'jw_token' => $jw_token,
        		    'qrcodeUrl' => $longUrl . '&t=' . $timeNum . '&f=qr',
        		    't' => $timeNum
    		    );
            }else{
                
                // 无结果
                $result = array(
        		    'code' => 204,
        		    'msg' => '获取失败'
    		    );
            }
        }
    	
    }else{
        
        // 未登录
        $result = array(
			'code' => 201,
            'msg' => '未登录'
		);
    }

	// 输出JSON
	echo json_encode($result,JSON_UNESCAPED_UNICODE);
	
?>