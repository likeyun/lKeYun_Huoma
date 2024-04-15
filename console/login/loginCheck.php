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
	
	// 接收参数
	$user_name = trim($_POST['user_name']);
	$user_pass = trim($_POST['user_pass']);
	
    // 验证是否已安装
    if(!file_exists('../Db.php')){
        
        // 未安装
        $result = array('code' => 404, 'msg' => '请安装后再登录...');
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        exit;
    }
	
	// sql防注入
    if(
        preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$user_name) || 
        preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$user_pass)){
            
            $result = array(
		        'code' => 203,
                'msg' => '你输入的内容包含了一些不安全字符'
	        );
	        echo json_encode($result,JSON_UNESCAPED_UNICODE);
	        exit;
    }else if(
        preg_match("/(and|or|select|update|drop|DROP|insert|create|delete|where|join|script|set)/i",$user_name) || 
        preg_match("/(and|or|select|update|drop|DROP|insert|create|delete|where|join|script|set)/i",$user_pass)
    ){
        
        $result = array(
	        'code' => 203,
            'msg' => '你输入的内容包含了一些不安全字符'
        );
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        exit;
    }
	
    // 过滤参数
    if(empty($user_name) || !isset($user_name)){
        
        $result = array(
		    'code' => 203,
            'msg' => '账号未填写'
	    );
    }else if(preg_match("/[\x7f-\xff]/", $user_name)){
        
        $result = array(
		    'code' => 203,
            'msg' => '账号不能存在中文'
	    );
    }else if(preg_match("/[\',:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$user_name)){
        
        $result = array(
		    'code' => 203,
            'msg' => '账号不能存在特殊字符'
	    );
    }else if(empty($user_pass) || !isset($user_pass)){
        
        $result = array(
		    'code' => 203,
            'msg' => '密码未填写'
	    );
    }else if(preg_match("/[\x7f-\xff]/", $user_pass)){
        
        $result = array(
		    'code' => 203,
            'msg' => '密码不能存在中文'
	    );
    }else if(preg_match("/[\',:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$user_pass)){
        
        $result = array(
		    'code' => 203,
            'msg' => '密码不能存在特殊字符'
	    );
    }else{
        
        // 数据库配置
    	include '../Db.php';
    
    	// 实例化类
    	$db = new DB_API($config);
    
    	// 数据库huoma_user表
    	$huoma_user = $db->set_table('huoma_user');
    	
        // 验证账号密码
        $checkUser = ['user_name'=>$user_name];
        $checkUserResult = $huoma_user->find($checkUser);
        
        // 账号（数据库的账号）
        $username = json_decode(json_encode($checkUserResult))->user_name;
        
        // 密码（数据库的密码）
        $userpass = json_decode(json_encode($checkUserResult))->user_pass;
        
        // 操作结果
        if($username == $user_name && $userpass == MD5($user_pass)){
            
            // 验证账号的状态
            $user_status = json_decode(json_encode($checkUserResult))->user_status;
            
            if($user_status == 1){
                
                // 正常
                // 设置SESSION有效期为7天（604800秒）
                ini_set('session.gc_maxlifetime', 604800);

                // 设置SESSION
                session_cache_expire(10080);
                session_start();
                $_SESSION["yinliubao"] = $user_name;
                
                // 保存一个持久的COOKIE，用于识别用户
                $cookie_value = $user_name.'_'.md5(time());
                setcookie($user_name, $cookie_value, time() + (7 * 24 * 3600), '/'); // 保存7天
                
                // 账号、密码都正确、账号状态正常
                $result = array(
                    'code' => 200,
                    'msg' => '登录成功'
                );
                
                // 登录日志
                $loginLogFile = "loginlog.txt";
                $loginLogData = 'Time：' .date('Y-m-d H:i:s'). '，Ip：' .$_SERVER['REMOTE_ADDR']. '，User：' . $user_name . PHP_EOL . '';
                file_put_contents($loginLogFile, $loginLogData, FILE_APPEND);
            }else{
                
                // 停用
                // 账号状态停用
                $result = array(
                    'code' => 202,
                    'msg' => '账号已被管理员停用'
                );
            }
        }else if(json_decode(json_encode($checkUserResult))->user_name !== $user_name && json_decode(json_encode($checkUserResult))->user_pass !== MD5($user_pass)){
            
            // 账号密码错误
            $result = array(
                'code' => 202,
                'msg' => '账号密码错误'
            );
        }else if(json_decode(json_encode($checkUserResult))->user_name !== $user_name){
            
            // 账号错误
            $result = array(
                'code' => 202,
                'msg' => '账号错误'
            );
        }else if(json_decode(json_encode($checkUserResult))->user_pass !== MD5($user_pass)){
            
            // 密码错误
            $result = array(
                'code' => 202,
                'msg' => '密码错误'
            );
        }
    }

	// 输出JSON
	echo json_encode($result,JSON_UNESCAPED_UNICODE);
	
?>