<?php
    
    /**
     * 状态码说明
     * 200 成功
     * 201 未登录
     * 202 失败
     * 203 空值
     */

	// 页面编码
	header("Content-type:application/json");
	ini_set("display_errors", "Off");
	
    // 获取参数
    $db_host = trim($_POST['db_host']);
    $db_user = trim($_POST['db_user']);
    $db_pass = trim($_POST['db_pass']);
    $db_name = trim($_POST['db_name']);
    $user_email = trim($_POST['user_email']);
    $user_name = trim($_POST['user_name']);
    $user_pass = trim($_POST['user_pass']);
    $install_folder = trim($_POST['install_folder']);
    
    // sql防注入
    if(
        preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$user_name) || 
        preg_match("/[\',:;*?~`!#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$user_email) || 
        preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$user_pass)){
            
            $result = array(
		        'code' => 203,
                'msg' => '你输入的管理员邮箱、账号、密码可能包含了一些不安全字符，请更换~'
	        );
	        echo json_encode($result,JSON_UNESCAPED_UNICODE);
	        exit;
    }else if(
        preg_match("/(and|or|select|update|drop|DROP|insert|create|delete|like|where|join|script|set)/i",$user_name) || 
        preg_match("/(and|or|select|update|drop|DROP|insert|create|delete|like|where|join|script|set)/i",$user_pass)
    ){
        
        $result = array(
	        'code' => 203,
            'msg' => '你输入的管理员账号、密码包含了一些不安全字符'
        );
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // 过滤参数
    if(empty($db_host) || !isset($db_host)){
        
        $result = array(
            'code' => 203,
            'msg' => '数据库地址未填写（无需带3306端口号）'
        );
    }else if(empty($db_host) || !isset($db_user)){
        
        $result = array(
            'code' => 203,
            'msg' => '数据库账号未填写'
        );
    }else if(empty($db_pass) || !isset($db_pass)){
        
        $result = array(
            'code' => 203,
            'msg' => '数据库密码未填写'
        );
    }else if(empty($db_name) || !isset($db_name)){
        
        $result = array(
            'code' => 203,
            'msg' => '数据库名称未填写'
        );
    }else if(empty($user_email) || !isset($user_email)){
        
        $result = array(
            'code' => 203,
            'msg' => '管理员邮箱未填写'
        );
    }else if(empty($user_name) || !isset($user_name)){
        
        $result = array(
            'code' => 203,
            'msg' => '管理员账号未填写'
        );
    }else if(strlen($user_name) < 5){
        
        $result = array(
            'code' => 203,
            'msg' => '账号不得小于5位数'
        );
    }else if(strlen($user_name) > 15){
        
        $result = array(
            'code' => 203,
            'msg' => '账号不得大于15位数'
        );
    }else if(preg_match("/[\x7f-\xff]/", $user_name)){
    
        $result = array(
		    'code' => 203,
            'msg' => '账号不能存在中文'
	    );
    }else if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$user_name)){
    
        $result = array(
		    'code' => 203,
            'msg' => '账号不能存在特殊字符'
	    );
    }else if(empty($user_pass) || !isset($user_pass)){
        
        $result = array(
            'code' => 203,
            'msg' => '管理员密码未填写'
        );
    }else if(strlen($user_pass) < 5){
            
        $result = array(
            'code' => 203,
            'msg' => '密码不得小于5位数'
        );
    }else if(strlen($user_pass) > 32){
        
        $result = array(
            'code' => 203,
            'msg' => '密码不得大于32位数'
        );
    }else if(preg_match("/[\x7f-\xff]/", $user_pass)){
    
        $result = array(
		    'code' => 203,
            'msg' => '密码不能存在中文'
	    );
    }else if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$user_pass)){
    
        $result = array(
		    'code' => 203,
            'msg' => '密码不能存在特殊字符'
	    );
    }else if(empty($install_folder) || !isset($install_folder)){
        
        $result = array(
            'code' => 203,
            'msg' => '安装目录级别未选择'
        );
    }else{
        
        // 验证数据库地址、账号、密码
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        
        // 根据数据库连接返回的错误信息判断连接失败的原因
        if($conn->connect_error == 'Connection timed out'){
            
            // 连接超时
            $result = array(
                'code' => 202,
                'msg' => '连接超时，可能是数据库地址有误'
            );
        }else if(preg_match("/getaddrinfo failed/", $conn->connect_error)){
            
            // 数据库地址有误
            $result = array(
                'code' => 202,
                'msg' => '数据库地址有误'
            );
        }else if(preg_match("/using password/", $conn->connect_error)){
            
            // 数据库账号或密码有误
            $result = array(
                'code' => 202,
                'msg' => '数据库账号或密码有误'
            );
        }else{
            
            // 用户
            $huoma_user = "CREATE TABLE `huoma_user` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `user_id` int(10) DEFAULT NULL COMMENT '用户ID',
              `user_name` varchar(32) DEFAULT NULL COMMENT '账号',
              `user_pass` varchar(64) DEFAULT NULL COMMENT '密码',
              `user_email` text COMMENT '邮箱',
              `user_mb_ask` text COMMENT '密保问题',
              `user_mb_answer` varchar(32) DEFAULT NULL COMMENT '密保答案',
              `user_creat_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '注册时间',
              `user_expire` varchar(32) DEFAULT NULL COMMENT '到期时间',
              `user_admin` int(2) DEFAULT '2' COMMENT '管理权限（1是 2否）',
              `user_manager` varchar(32) DEFAULT NULL COMMENT '账号管理者',
              `user_beizhu` varchar(64) DEFAULT NULL COMMENT '备注信息',
              `user_status` int(2) NOT NULL DEFAULT '1' COMMENT '账号状态（1可用 2停用）'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            
            // 设置管理员账号和密码
            $huoma_user_Data = "INSERT INTO `huoma_user` (`user_id`, `user_name`, `user_pass`, `user_expire`, `user_email`, `user_mb_ask`, `user_admin`, `user_manager`, `user_beizhu`) VALUES (100000, '".$user_name."', '".MD5($user_pass)."', '2033-12-31 23:59:59', '".$user_email."','请选择密保问题', 1, '".$user_name."', '超级管理员')";
            
            // 群活码
            $huoma_qun = "CREATE TABLE `huoma_qun` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `qun_id` int(10) DEFAULT NULL COMMENT '群ID',
              `qun_title` varchar(64) DEFAULT NULL COMMENT '群标题',
              `qun_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态（1开启 2关闭）默认1',
              `qun_creat_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `qun_pv` int(10) NOT NULL DEFAULT '0' COMMENT '访问量',
              `qun_today_pv` varchar(32) DEFAULT NULL COMMENT '今天访问量',
              `qun_qc` int(2) NOT NULL DEFAULT '2' COMMENT '去重（1开启 2关闭）默认2',
              `qun_notify` varchar(32) DEFAULT NULL COMMENT '通知渠道',
              `qun_rkym` text COMMENT '入口域名',
              `qun_ldym` text COMMENT '落地域名',
              `qun_dlym` text COMMENT '短链域名',
              `qun_kf` text COMMENT '客服二维码',
              `qun_kf_status` int(2) NOT NULL DEFAULT '2' COMMENT '客服开启状态（1开启 2关闭）默认2',
              `qun_safety` int(2) NOT NULL DEFAULT '1' COMMENT '顶部安全提示（1显 2隐）',
              `qun_beizhu` text COMMENT '群备注',
              `qun_key` varchar(10) DEFAULT NULL COMMENT '短链接Key',
              `qun_creat_user` varchar(32) DEFAULT NULL COMMENT '创建者账号'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='群活码列表'";
            
            // 群活码下的群二维码
            $huoma_qun_zima = "CREATE TABLE `huoma_qun_zima` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `qun_id` int(10) DEFAULT NULL COMMENT '群活码ID',
              `zm_id` int(10) DEFAULT NULL COMMENT '群子码ID',
              `zm_yz` int(10) NOT NULL DEFAULT '200' COMMENT '阈值',
              `zm_pv` int(10) NOT NULL DEFAULT '0' COMMENT '访问量',
              `zm_qrcode` text COMMENT '二维码URL',
              `zm_leader` varchar(32) DEFAULT NULL COMMENT '群主微信号',
              `zm_update_time` varchar(32) DEFAULT NULL COMMENT '更新时间',
              `zm_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态（1开 2关）'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='群活码下的群二维码'";
            
            // 客服码
            $huoma_kf = "CREATE TABLE `huoma_kf` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `kf_id` int(10) DEFAULT NULL COMMENT '客服码ID',
              `kf_title` varchar(64) DEFAULT NULL COMMENT '客服标题或昵称',
              `kf_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态（1正常 2停用）',
              `kf_creat_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `kf_pv` int(10) NOT NULL DEFAULT '0' COMMENT '访问量',
              `kf_today_pv` varchar(32) DEFAULT NULL COMMENT '今天的访问量',
              `kf_rkym` text COMMENT '入口域名',
              `kf_ldym` text COMMENT '落地域名',
              `kf_dlym` text COMMENT '短链域名',
              `kf_model` int(2) DEFAULT NULL COMMENT '展示模式（1阈值 2随机）',
              `kf_online` int(2) NOT NULL DEFAULT '2' COMMENT '在线状态（1显 2隐）',
              `kf_onlinetimes` text COMMENT '在线时间JSON配置',
              `kf_key` varchar(10) DEFAULT NULL COMMENT '短链接Key',
              `kf_safety` int(2) NOT NULL DEFAULT '1' COMMENT '顶部安全提示（1显 2隐）',
              `kf_beizhu` text COMMENT '备注',
              `kf_creat_user` varchar(32) DEFAULT NULL COMMENT '创建者账号'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客服码'";
            
            // 客服码下的微信二维码
            $huoma_kf_zima = "CREATE TABLE `huoma_kf_zima` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `kf_id` int(10) DEFAULT NULL COMMENT '客服码ID',
              `zm_id` int(10) DEFAULT NULL COMMENT '子码ID',
              `zm_yz` int(10) DEFAULT '0' COMMENT '阈值',
              `zm_pv` int(10) NOT NULL DEFAULT '0' COMMENT '访问量',
              `zm_qrcode` text COMMENT '二维码URL',
              `zm_num` varchar(32) DEFAULT NULL COMMENT '客服微信号',
              `zm_update_time` varchar(32) DEFAULT NULL COMMENT '更新时间',
              `zm_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态（1开 2关）	'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客服码下的微信二维码'";
            
            // 域名
            $huoma_domain = "CREATE TABLE `huoma_domain` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `domain_id` int(10) DEFAULT NULL COMMENT '域名ID',
              `domain_type` int(2) DEFAULT NULL COMMENT '域名类型（1入口 2落地 3短链 4备用）',
              `domain` text COMMENT '域名'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='域名'";
            
            // 获取http协议类型
            $HTTP_TYPE = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
            
            // 添加默认的域名
            $huoma_domain_Data = "INSERT INTO `huoma_domain` (`domain_id`, `domain_type`, `domain`) VALUES
            (100000, 1, '".$HTTP_TYPE.$_SERVER['HTTP_HOST']."'),
            (100001, 2, '".$HTTP_TYPE.$_SERVER['HTTP_HOST']."'),
            (100002, 3, '".$HTTP_TYPE.$_SERVER['HTTP_HOST']."'),
            (100003, 4, '".$HTTP_TYPE.$_SERVER['HTTP_HOST']."')";
            
            // 渠道码
            $huoma_channel = "CREATE TABLE `huoma_channel` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `channel_id` int(10) DEFAULT NULL COMMENT '渠道ID',
              `channel_title` varchar(64) DEFAULT NULL COMMENT '渠道标题',
              `channel_status` int(2) DEFAULT '1' COMMENT '状态（1正常 2停用）',
              `channel_creat_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `channel_pv` int(10) DEFAULT '0' COMMENT '访问量',
              `channel_today_pv` varchar(32) DEFAULT NULL COMMENT '今天的访问量',
              `channel_rkym` text COMMENT '入口域名',
              `channel_ldym` text COMMENT '落地域名',
              `channel_dlym` text COMMENT '短链域名',
              `channel_key` varchar(10) DEFAULT NULL COMMENT '短链接key',
              `channel_url` text COMMENT '渠道目标链接',
              `channel_creat_user` varchar(32) DEFAULT NULL COMMENT '创建者账号'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='渠道码'";
            
            // 渠道码数据
            $huoma_channel_data = "CREATE TABLE `huoma_channel_data` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `data_id` int(10) DEFAULT NULL COMMENT '数据ID',
              `channel_id` int(10) DEFAULT NULL COMMENT '渠道ID',
              `data_referer` text COMMENT '数据来源',
              `data_device` varchar(32) DEFAULT NULL COMMENT '来源设备',
              `data_ip` varchar(32) DEFAULT NULL COMMENT '数据来源IP',
              `data_pv` int(10) DEFAULT '0' COMMENT '访问量',
              `data_creat_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '数据来源的时间'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='渠道码数据'";
            
            // 渠道码黑名单IP
            $huoma_channel_accessdenied = "CREATE TABLE `huoma_channel_accessdenied` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `data_ip` varchar(32) DEFAULT NULL COMMENT 'IP',
              `accessdenied_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '加入时间',
              `add_user` varchar(32) DEFAULT NULL COMMENT '操作者账号'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='渠道码黑名单IP'";
            
            // 短网址
            $huoma_dwz = "CREATE TABLE `huoma_dwz` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `dwz_id` int(10) DEFAULT NULL COMMENT '短网址ID',
              `dwz_title` varchar(32) DEFAULT NULL COMMENT '标题',
              `dwz_key` varchar(10) DEFAULT NULL COMMENT '短网址Key',
              `dwz_creat_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `dwz_pv` int(10) NOT NULL DEFAULT '0' COMMENT '访问量',
              `dwz_today_pv` varchar(32) DEFAULT NULL COMMENT '今天访问量',
              `dwz_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态（1正常 2停用）',
              `dwz_url` text DEFAULT NULL COMMENT '目标链接',
              `dwz_type` int(2) DEFAULT NULL COMMENT '访问限制',
              `dwz_rkym` text COMMENT '入口域名',
              `dwz_zzym` text COMMENT '中转域名',
              `dwz_dlym` text COMMENT '短链域名',
              `dwz_android_url` text DEFAULT NULL COMMENT 'Android设备目标链接',
              `dwz_ios_url` text DEFAULT NULL COMMENT 'iOS设备目标链接',
              `dwz_windows_url` text DEFAULT NULL COMMENT 'Windows设备目标链接',
              `dwz_creat_user` varchar(32) DEFAULT NULL COMMENT '创建者'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='短网址'";
            
            // 短网址API
            $huoma_dwz_apikey = "CREATE TABLE `huoma_dwz_apikey` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `apikey_user` varchar(32) DEFAULT NULL COMMENT '用户名',
              `apikey_id` int(10) DEFAULT NULL COMMENT 'ID',
              `apikey_ip` varchar(32) DEFAULT NULL COMMENT '白名单IP',
              `apikey` varchar(32) DEFAULT NULL COMMENT '开放接口ApiKey',
              `apikey_secrete` varchar(64) DEFAULT NULL COMMENT '开放接口密钥',
              `apikey_creat_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `apikey_expire` varchar(32) DEFAULT NULL COMMENT '到期时间',
              `apikey_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态（1正常 2停用）',
              `apikey_quota` int(20) DEFAULT '100000' COMMENT '请求配额（最大次数）',
              `apikey_num` int(20) NOT NULL DEFAULT '0' COMMENT '请求次数',
              `apikey_creat_user` varchar(32) DEFAULT NULL COMMENT '创建者'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='短网址API'";
            
            // 淘宝客中间页
            $huoma_tbk = "CREATE TABLE `huoma_tbk` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `zjy_id` varchar(10) DEFAULT NULL COMMENT '中间页ID',
              `zjy_short_title` varchar(32) DEFAULT NULL COMMENT '短标题',
              `zjy_long_title` text COMMENT '长标题',
              `zjy_tkl` varchar(64) DEFAULT NULL COMMENT '淘口令',
              `zjy_rkym` text COMMENT '入口域名',
              `zjy_ldym` text COMMENT '落地域名',
              `zjy_dlym` text COMMENT '短链域名',
              `zjy_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `zjy_pv` varchar(10) NOT NULL DEFAULT '0' COMMENT '访问量',
              `zjy_copyNum` int(10) DEFAULT '0' COMMENT '复制次数',
              `zjy_original_cost` varchar(10) DEFAULT NULL COMMENT '原价',
              `zjy_discounted_price` varchar(10) DEFAULT NULL COMMENT '券后价',
              `zjy_goods_img` text COMMENT '商品主图',
              `zjy_goods_link` text COMMENT '商品链接',
              `zjy_key` varchar(10) DEFAULT NULL COMMENT '短链接',
              `zjy_create_user` varchar(32) DEFAULT NULL COMMENT '创建者'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='淘宝客中间页'";
            
            // 淘宝客中间页配置
            $huoma_tbk_config = "CREATE TABLE `huoma_tbk_config` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `zjy_config_appkey` varchar(64) DEFAULT NULL COMMENT '折淘客appkey',
              `zjy_config_sid` varchar(32) DEFAULT NULL COMMENT '折淘客sid',
              `zjy_config_pid` varchar(64) DEFAULT NULL COMMENT '你的pid',
              `zjy_config_tbname` varchar(32) DEFAULT NULL COMMENT '淘宝账号',
              `zjy_config_user` varchar(32) DEFAULT NULL COMMENT '你的引流宝账号'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='淘宝客中间页配置'";
            
            // 分享卡片
            $huoma_shareCard = "CREATE TABLE `huoma_shareCard` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `shareCard_id` int(10) DEFAULT NULL COMMENT '卡片ID',
              `shareCard_title` varchar(64) DEFAULT NULL COMMENT '标题',
              `shareCard_desc` text COMMENT '摘要',
              `shareCard_img` text COMMENT '分享缩略图',
              `shareCard_ldym` text COMMENT '落地域名',
              `shareCard_url` text COMMENT '目标链接',
              `shareCard_pv` int(10) NOT NULL DEFAULT '0' COMMENT '访问量',
              `shareCard_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `shareCard_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态（1正常 2停用）',
              `shareCard_create_user` varchar(32) DEFAULT NULL COMMENT '创建者'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分享卡片'";
            
            // 分享卡片配置
            $huoma_shareCardConfig = "CREATE TABLE `huoma_shareCardConfig` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `appid` varchar(32) DEFAULT NULL COMMENT '公众号appid',
              `appsecret` varchar(64) DEFAULT NULL COMMENT '公众号appsecret',
	      `wxCallback_url` varchar(64) DEFAULT NULL COMMENT '彩虹的微信公众号多域名回调系统自建域名',
              `access_token` text COMMENT 'access_token',
              `access_token_expire_time` varchar(32) DEFAULT NULL COMMENT 'access_token_expire_time',
              `jsapi_ticket` text COMMENT 'jsapi_ticket',
              `jsapi_ticket_expire_time` varchar(32) DEFAULT NULL COMMENT 'jsapi_ticket_expire_time'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分享卡片配置'";
            
            // 域名检测
            $huoma_domainCheck = "CREATE TABLE `huoma_domainCheck` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `domainCheck_status` int(1) NOT NULL DEFAULT '2' COMMENT '状态',
              `domainCheck_channel` varchar(32) DEFAULT NULL COMMENT '通知渠道',
              `domainCheck_byym` text COMMENT '备用域名'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='域名检测'";
            
            // 添加默认域名检测配置
            $huoma_domainCheck_Data = "INSERT INTO `huoma_domainCheck` (`domainCheck_status`,`domainCheck_channel`,`domainCheck_byym`) VALUES (2,'企业微信','".$HTTP_TYPE.$_SERVER['HTTP_HOST']."')";
            
            // 24小时访问量统计
            $huoma_hourNum = "CREATE TABLE `huoma_hourNum` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `hourNum_type` varchar(32) DEFAULT NULL COMMENT '分类',
              `hourNum_date` varchar(32) DEFAULT NULL COMMENT '日期',
              `hourNum_hour` varchar(2) DEFAULT NULL COMMENT '小时',
              `hourNum_pv` int(10) NOT NULL DEFAULT '0' COMMENT '访问量'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='24小时访问量统计'";
            
            // IP统计
            $huoma_ip = "CREATE TABLE `huoma_ip` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `qun_ip` int(10) NOT NULL DEFAULT '0' COMMENT '群活码IP访问次数',
              `kf_ip` int(10) NOT NULL DEFAULT '0' COMMENT '客服码IP访问次数',
              `channel_ip` int(10) NOT NULL DEFAULT '0' COMMENT '渠道码IP访问次数',
              `dwz_ip` int(10) NOT NULL DEFAULT '0' COMMENT '短网址IP访问次数',
              `zjy_ip` int(10) NOT NULL DEFAULT '0' COMMENT '中间页IP访问次数',
              `shareCard_ip` int(10) NOT NULL DEFAULT '0' COMMENT '分享卡片IP访问次数',
              `multiSPA_ip` int(10) NOT NULL DEFAULT '0' COMMENT '多项单页IP访问次数',
              `ip_create_time` varchar(32) DEFAULT NULL COMMENT 'IP记录日期'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='IP统计'";
            
            // IP临时记录
            $huoma_ip_temp = "CREATE TABLE `huoma_ip_temp` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `ip` varchar(32) DEFAULT NULL COMMENT 'IP地址',
              `create_date` varchar(32) DEFAULT NULL COMMENT '访问日期',
              `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '访问时间',
              `from_page` varchar(32) DEFAULT NULL COMMENT '来自的页面'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='IP临时记录'";
            
            // 通知渠道配置
            $huoma_notification = "CREATE TABLE `huoma_notification` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `corpid` varchar(64) DEFAULT NULL COMMENT '企业微信corpid',
              `corpsecret` varchar(64) DEFAULT NULL COMMENT '企业微信corpsecret',
              `touser` varchar(32) DEFAULT NULL COMMENT '企业微信接收者ID',
              `agentid` varchar(32) DEFAULT NULL COMMENT '企业微信应用ID',
              `bark_url` text DEFAULT NULL COMMENT 'bark推送链接',
              `email_acount` text DEFAULT NULL COMMENT '邮件发送端账号',
              `email_pwd` text DEFAULT NULL COMMENT '邮件发送端授权码',
              `email_smtp` varchar(64) DEFAULT NULL COMMENT '邮件服务器',
              `email_port` varchar(32) DEFAULT NULL COMMENT '邮件服务器端口',
              `email_receive` text DEFAULT NULL COMMENT '接收邮件的邮箱',
              `SendKey` text DEFAULT NULL COMMENT 'Server酱SendKey',
              `http_url` text DEFAULT NULL COMMENT '接收POST数据的URL'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='通知渠道配置'";
            
            // 添加默认通知渠道配置
            $huoma_notification_Data = "INSERT INTO `huoma_notification` (
            `corpid`,`corpsecret`,`touser`,`agentid`,
            `bark_url`,`email_acount`,`email_pwd`,`email_smtp`,
            `email_port`,`email_receive`,`SendKey`,`http_url`) VALUES (
            '未设置','未设置','未设置','未设置',
            '未设置','未设置','未设置','未设置',
            '未设置','未设置','未设置','未设置')";
            
            // 素材库
            $huoma_sucai = "CREATE TABLE `huoma_sucai` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `sucai_id` int(10) DEFAULT NULL COMMENT '素材ID',
              `sucai_filename` text DEFAULT NULL COMMENT '文件名',
              `sucai_beizhu` text DEFAULT NULL COMMENT '素材备注',
              `sucai_upload_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '上传时间',
              `sucai_upload_user` varchar(32) DEFAULT NULL COMMENT '上传用户',
              `sucai_type` int(10) DEFAULT NULL COMMENT '素材类型（1图片 2视频 3音频）',
              `sucai_size` int(10) DEFAULT NULL COMMENT '素材大小'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='素材库'";
            
            // 多项单页
            $huoma_tbk_mutiSPA = "CREATE TABLE `huoma_tbk_mutiSPA` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `multiSPA_id` int(10) DEFAULT NULL COMMENT '单页ID',
              `multiSPA_title` varchar(64) DEFAULT NULL COMMENT '单页标题',
              `multiSPA_rkym` text DEFAULT NULL COMMENT '入口域名',
              `multiSPA_ldym` text DEFAULT NULL COMMENT '落地域名',
              `multiSPA_dlym` text DEFAULT NULL COMMENT '短链域名',
              `multiSPA_key` varchar(10) DEFAULT NULL COMMENT '短网址Key',
              `multiSPA_project` text DEFAULT NULL COMMENT '项目HTML',
              `multiSPA_img` text DEFAULT NULL COMMENT '主图Url',
              `multiSPA_pv` int(10) DEFAULT '0' COMMENT '访问量',
              `multiSPA_addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `create_user` varchar(32) DEFAULT NULL COMMENT '创建用户'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='多项单页'";
            
            // 连接成功
            // 检查是否已经安装
            // 1、检查是否存在Db.php或安装锁
            if(file_exists('./install.lock') || file_exists('../console/Db.php')){
                
                // 存在Db.php
                // 已安装
                $result = array(
                    'code' => 202,
                    'msg' => '请勿重复安装！如需重新安装请删除 /install/install.lock 和 /console/Db.php'
                );
            }else{
                
                // 不存在Db.php
                // 开始创建表
                if(
                    $conn->query($huoma_user) === TRUE && 
                    $conn->query($huoma_user_Data) === TRUE && 
                    $conn->query($huoma_qun) === TRUE && 
                    $conn->query($huoma_qun_zima) === TRUE && 
                    $conn->query($huoma_kf) === TRUE && 
                    $conn->query($huoma_kf_zima) === TRUE && 
                    $conn->query($huoma_domain) === TRUE && 
                    $conn->query($huoma_domain_Data) === TRUE && 
                    $conn->query($huoma_channel) === TRUE && 
                    $conn->query($huoma_channel_data) === TRUE && 
                    $conn->query($huoma_channel_accessdenied) === TRUE && 
                    $conn->query($huoma_dwz) === TRUE && 
                    $conn->query($huoma_dwz_apikey) === TRUE && 
                    $conn->query($huoma_tbk) === TRUE && 
                    $conn->query($huoma_tbk_config) === TRUE && 
                    $conn->query($huoma_shareCard) === TRUE && 
                    $conn->query($huoma_shareCardConfig) === TRUE &&
                    $conn->query($huoma_domainCheck) === TRUE && 
                    $conn->query($huoma_domainCheck_Data) === TRUE && 
                    $conn->query($huoma_hourNum) === TRUE && 
                    $conn->query($huoma_ip) === TRUE && 
                    $conn->query($huoma_ip_temp) === TRUE && 
                    $conn->query($huoma_notification) === TRUE && 
                    $conn->query($huoma_notification_Data) === TRUE && 
                    $conn->query($huoma_sucai) === TRUE && 
                    $conn->query($huoma_tbk_mutiSPA) === TRUE){

                    // 淘宝客配置默认数据
                    $huoma_tbk_config_Data = "INSERT INTO `huoma_tbk_config` (`zjy_config_appkey`, `zjy_config_sid`, `zjy_config_pid`, `zjy_config_tbname`, `zjy_config_user`) VALUES ('未设置', '未设置', '未设置', '未设置', '$user_name')";
                    $conn->query($huoma_tbk_config_Data);
                    
                    // 分享卡片配置默认数据
                    $huoma_shareCardConfig_Data = "INSERT INTO `huoma_shareCardConfig` (`appid`, `appsecret`) VALUES ('未设置', '未设置')";
                    $conn->query($huoma_shareCardConfig_Data);
                    
                    // 生成数据库配置文件
                    $Db_config = [
                        'db_host' => $db_host,
                        'db_port' => 3306,
                        'db_name' => $db_name,
                        'db_user' => $db_user,
                        'db_pass' => $db_pass,
                        'folderNum' => $install_folder,
                        'version' => '2.0.0'
                    ];
                    
                    // 生成Db.php文件内容
                    $fileContent = "<?php\n\n";
                    $fileContent .= "// 数据库操作类\n";
                    $fileContent .= "include 'DbClass.php';\n\n";
                    $fileContent .= "// 数据库配置\n";
                    $fileContent .= '$config = ' . var_export($Db_config, true) . ";\n";
                    $fileContent .= "?>";
                    
                    // 将内容写入Db.php文件
                    $filePath = "../console/Db.php";
                    file_put_contents($filePath, $fileContent);
                    
                    // 创建安装锁
                    file_put_contents('./install.lock','安装锁');
                    
                    // 暂停5秒
                    sleep(5);
                    
                    // 安装成功
                    $result = array(
                        'code' => 200,
                        'msg' => '安装成功'
                    );
                    
                }else if(preg_match("/already exists/", $conn->error)){
                    
                    // 存在huoma_前缀的表
                    $result = array(
                        'code' => 202,
                        'msg' => '请勿重复安装！如需重新安装请删除huoma_前缀的表！'
                    );
                }else{
                    
                    // 安装失败
                    $result = array(
                        'code' => 202,
                        'msg' => '安装失败，报错信息：（'.$conn->error.'）如果没有报错信息，请按F12打开控制台查看网络请求的报错进行调试！'
                    );
                }
            }
            
        }
    }
    
    // 输出JSON
	echo json_encode($result,JSON_UNESCAPED_UNICODE);
	
?>
