window.onload = function (){
    
    // 获取登录状态
    getLoginStatus();
    
    // 获取IP
    getIPTotal();
    
    // 加载项目说明
    projectInfo();
    
    // 自动刷新状态
    var freshen = queryURLParams(window.location.href).f;
    if(freshen !== 'undefined'){
        
        // 开启
        if(freshen == 1){
            
            // 关闭自动刷新
            $('#right .data-card .chart-pannel .autofreshen').html('<a href="./">关闭自动刷新</a>');
            
            // 刷新（1分钟刷新一次）
            setInterval('getPvTotal("群活码","qun")',60000);
        }else{
            
            // 开启自动刷新
            $('#right .data-card .chart-pannel .autofreshen').html(
                '<a href="?f=1" title="每分钟刷新一次首页数据">开启自动刷新</a>'
            );
        }
        
    }
}

// 获取登录状态
function getLoginStatus(){
    
    $.ajax({
        type: "POST",
        url: "../login/getLoginStatus.php",
        success: function(res){
            
            // 成功
            // 账号及版本信息
            if(res.code == 200){
                
                // 已登录
                var $account = $(
                    '<div class="version">'+res.version+'</div>' +
                    '<div class="user_name">'+res.user_name+' <span onclick="exitLogin();" class="exitLogin">退出</span></div>'
                );
                $(".left .account").html($account);
                $("#right .data-card .data-content").css('display','block');
                $("#right .data-card .loading").css('display','none');
                
            }else{
                
                // 未登录
                var $account = $(
                    '<div class="version">'+res.version+'</div>' +
                    '<div class="user_name">未登录</div>'
                );
                $(".left .account").html($account);
                $("#right .data-card .data-content").css('display','none');
                $("#right .data-card .loading").css('display','block');
                noData('未登录');
            }
        },
        error: function() {
            
            // 服务器发生错误
            errorPage('getLoginStatus.php');
        }
    });
}

// 注销登录
function exitLogin(){
    
    $.ajax({
        type: "POST",
        url: "../login/exitLogin.php",
        success: function(res){
            
            // 成功
            if(res.code == 200){
                
                // 刷新
                location.reload();
            }
        },
        error: function() {
            
            // 服务器发生错误
            errorPage('exitLogin.php');
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    
    // 数据卡HTML模板
    $swiper_wrapper = 
    `<div class="swiper-wrapper" title="点击切换图表">
        <div class="swiper-slide swiper-slide-selected" data-type="qun">
            <div class="card-title">
                <div class="num-title">群活码</div>
                <div class="card-num qunNum"> - </div>
            </div>
            <div class="ip-num">
                <span class="ip-today">
                    <span class="num-title">今日uv <span class="td-qunIP"> - </span></span>
                </span>
                <span class="ip-yesterday">
                    <span class="num-title">昨日uv <span class="ipNum yt-qunIP"> - </span></span>
                </span>
            </div>
        </div>
        
        <div class="swiper-slide" data-type="kf">
            <div class="card-title">
                <div class="num-title">客服码</div>
                <div class="card-num kfNum"> - </div>
            </div>
            <div class="ip-num">
                <span class="ip-today">
                    <span class="num-title">今日uv <span class="ipNum td-kfIP"> - </span></span>
                </span>
                <span class="ip-yesterday">
                    <span class="num-title">昨日uv <span class="ipNum yt-kfIP"> - </span></span>
                </span>
            </div>
        </div>
        
        <div class="swiper-slide" data-type="channel">
            <div class="card-title">
                <div class="num-title">渠道码</div>
                <div class="card-num channelNum"> - </div>
            </div>
            <div class="ip-num">
                <span class="ip-today">
                    <span class="num-title">今日uv <span class="ipNum td-channelIP"> - </span></span>
                </span>
                <span class="ip-yesterday">
                    <span class="num-title">昨日uv <span class="ipNum yt-channelIP"> - </span></span>
                </span>
            </div>
        </div>
        
        <div class="swiper-slide" data-type="dwz">
            <div class="card-title">
                <div class="num-title">短网址</div>
                <div class="card-num dwzNum"> - </div>
            </div>
            <div class="ip-num">
                <span class="ip-today">
                    <span class="num-title">今日uv <span class="ipNum td-dwzIP"> - </span></span>
                </span>
                <span class="ip-yesterday">
                    <span class="num-title">昨日uv <span class="ipNum yt-dwzIP"> - </span></span>
                </span>
            </div>
        </div>
        
        <div class="swiper-slide" data-type="zjy">
            <div class="card-title">
                <div class="num-title">淘宝客</div>
                <div class="card-num zjyNum"> - </div>
            </div>
            <div class="ip-num">
                <span class="ip-today">
                    <span class="num-title">今日uv </span>
                </span>
                <span class="ip-yesterday">
                    <span class="num-title">昨日uv </span>
                </span>
            </div>
        </div>
        
        <div class="swiper-slide" data-type="shareCard">
            <div class="card-title">
                <div class="num-title">分享卡片</div>
                <div class="card-num shareCardNum"> - </div>
            </div>
            <div class="ip-num">
                <span class="ip-today">
                    <span class="num-title">今日uv <span class="ipNum td-shareCardIP"> - </span></span>
                </span>
                <span class="ip-yesterday">
                    <span class="num-title">昨日uv <span class="ipNum yt-shareCardIP"> - </span></span>
                </span>
            </div>
        </div>
        
        <div class="swiper-slide" data-type="multiSPA">
            <div class="card-title">
                <div class="num-title">多项单页</div>
                <div class="card-num multiNum"> - </div>
            </div>
            <div class="ip-num">
                <span class="ip-today">
                    <span class="num-title">今日uv <span class="ipNum td-multiIP"> - </span></span>
                </span>
                <span class="ip-yesterday">
                    <span class="num-title">昨日uv <span class="ipNum yt-multiIP"> - </span></span>
                </span>
            </div>
        </div>
    </div>`;
    
    // 将这个模板渲染到#right .data-card .data-chart .data-pannel .swiper
    $('#right .data-card .data-chart .data-pannel .swiper').html($swiper_wrapper);
    
    // 加载函数
    getPvTotal('群活码','qun');
    
    // 获取访问量
    function getPvTotal(label,type) {
        
        $.ajax({
            type: "POST",
            url: "./getIndexData.php?hourNum_type="+type+"&label="+label,
            success: function(res){
    
                // 200状态码
                if(res.code == 200){
                    
                    // if(res.user_admin == 2) {
                        
                    //     $('.data-chart').html('<div class="no_limit">非管理员无法查看数据</div>');
                    // }
                    
                    // 当日访问量
                    const dataMapping = {
                        qunNum: 'qun_pvTotal',
                        kfNum: 'kf_pvTotal',
                        channelNum: 'channel_pvTotal',
                        dwzNum: 'dwz_pvTotal',
                        zjyNum: 'zjy_pvTotal',
                        shareCardNum: 'shareCard_pvTotal',
                        multiNum: 'multiSPA_pvTotal',
                    };
                    
                    Object.entries(dataMapping).forEach(([elementClass, dataKey]) => {
                        const selector = `#right .data-card .data-chart .data-pannel .swiper .swiper-slide .${elementClass}`;
                        $(selector).text(res.pvTotals[dataKey]);
                    });

                    // 销毁Canvas图表
                    $('#eachHourPvChart').remove();
                    
                    // 渲染Canvas
                    $('#right .data-card .data-chart .chart-pannel .chart-view').append(
                        '<canvas id="eachHourPvChart" width="350" height="130"></canvas>'
                    );
                    
                    // 获取图表Canvas
                    var ctx = $('#eachHourPvChart');
                    
                    // 横坐标
                    var labelsArray = Array.from({ length: 24 }, (_, i) => `${i}h`);
                    
                    // 图表配置
                    var eachHourPvChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labelsArray, // 横坐标数据
                            datasets: res.chartData // 表配置、折线图数据（从后端获取）
                        }
                    });

                }else{
                    
                    // 未登录
                    if(res.code == 201){
                        
                        // 跳转到登录页面
                        jumpUrl('../login/');
                    }
                    
                    // 非200状态码
                    noData(res.msg);
                }
                
          },
          error: function(){
            
            // 发生错误
            errorPage('getIndexData.php')
          },
        });
    }
    
    // 获取元素
    const swiperWrapper = document.querySelector('.swiper-wrapper');
    const slides = document.querySelectorAll('.swiper-slide');
    const pagination = document.querySelector('.swiper-pagination');
    
    // 设置初始索引和宽度
    let currentIndex = 0;
    
    // 显示多少个卡片
    const slideWidth = slides[0].offsetWidth * 4;
    
    // 设置初始位置
    swiperWrapper.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
    
    $('#right .pre-swiper').css('display','none');
    
    // 向左切换
    $('#right .pre-swiper').click(function(){
        if(currentIndex > 0){
            currentIndex--;
            swiperWrapper.style.transform=`translateX(-${currentIndex*slideWidth}px)`;
            $('#right .pre-swiper').css('display','none');
            $('#right .next-swiper').css('display','block');
        }
    });
    
    // 向右切换
    $('#right .next-swiper').click(function(){
        if(currentIndex<slides.length/3-1){      
            currentIndex++;
            swiperWrapper.style.transform=`translateX(-${currentIndex*slideWidth}px)`;
        }
        $('#right .next-swiper').css('display','none');
        $('#right .pre-swiper').css('display','block');
    });
    
    // 图表切换
    $('#right .data-card .data-pannel .swiper .swiper-slide').click(function(){
        
        // 修改样式
        $(this).siblings().removeClass('swiper-slide-selected');
        $(this).addClass('swiper-slide-selected');
        
        // 修改图表数据
        var label = $(this)[0].innerText;
        var type = $(this)[0].dataset.type;
        
        // 获取访问量
        getPvTotal(label,type);
        
        // 设置URL
        window.history.pushState('', '', '#'+type);
    })
})

// 获取IP
function getIPTotal() {
    
    $.ajax({
        type: "POST",
        url: "./getipData.php",
        success: function(res){

            // 200状态码
            if(res.code == 200){
                
                // 当日访问量
                const todayIP = res.todayIP[0];
                const yesterdayIP = res.yesterdayIP[0];
                
                // 选择器缓存
                const $swiperSlide = $('#right .data-card .swiper .swiper-slide');
                const $ipNum = $swiperSlide.find('.ip-num');
                
                // 设置今天的IP
                $ipNum.find('.td-qunIP').text(todayIP.qun_ip);
                $ipNum.find('.td-kfIP').text(todayIP.kf_ip);
                $ipNum.find('.td-channelIP').text(todayIP.channel_ip);
                $ipNum.find('.td-dwzIP').text(todayIP.dwz_ip);
                $ipNum.find('.td-zjyIP').text(todayIP.zjy_ip);
                $ipNum.find('.td-shareCardIP').text(todayIP.shareCard_ip);
                $ipNum.find('.td-multiIP').text(todayIP.multiSPA_ip);
                
                // 设置昨天的IP
                $ipNum.find('.yt-qunIP').text(yesterdayIP.qun_ip);
                $ipNum.find('.yt-kfIP').text(yesterdayIP.kf_ip);
                $ipNum.find('.yt-channelIP').text(yesterdayIP.channel_ip);
                $ipNum.find('.yt-dwzIP').text(yesterdayIP.dwz_ip);
                $ipNum.find('.yt-zjyIP').text(yesterdayIP.zjy_ip);
                $ipNum.find('.yt-shareCardIP').text(yesterdayIP.shareCard_ip);
                $ipNum.find('.yt-multiIP').text(yesterdayIP.multiSPA_ip);
                
                // 获取7天的IP数据
                get7DaysIpData();
                
            }else{
                
                // 未登录
                if(res.code == 201){
                    
                    // 跳转到登录页面
                    jumpUrl('../login/');
                }
                
                noData(res.msg);
            }
            
      },
      error: function(){
        
        // 发生错误
        errorPage('getipData.php')
      },
    });
}

// 项目说明
function projectInfo(){
    const projectInfoElement = document.querySelector("#right .project-info");
    
    const links = [
    {
        title: "开源地址 >>",
        desc: "获取作者的正版源码及更新动态。",
        url: "https://github.com/likeyun/liKeYun_Ylb",
    },
    {
        title: "使用文档 >>",
        desc: "快速学习和了解正确的使用姿势。",
        url: "https://docs.qq.com/doc/DREdWVGJxeFFOSFhI",
    },
    {
        title: "开发文档 >>",
        desc: "阅读以进行二次开发和个性化修改。",
        url: "https://docs.qq.com/doc/DRE9aWlRqZUdFRWl1",
    },
    {
        title: "用户交流群 >>",
        desc: "加群讨论部署安装、使用、开发等话题。",
        url: "../../static/img/jiaQun.jpg",
    },
    {
        title: "反馈建议 >>",
        desc: "对本开源作品的反馈及开发建议。",
        url: "https://support.qq.com/product/453822",
    },
    {
        title: "作者博客 >>",
        desc: "关注作者的博客学习开发编程基础。",
        url: "https://segmentfault.com/u/tanking",
    },
    {
        title: "赞赏作者 >>",
        desc: "没有任何盈利，全靠赞赏支持继续维护。",
        url: "../../static/img/zansangma.jpg",
    },
    ];
    
    links.forEach((link) => {
        const linkElement = document.createElement("a");
        linkElement.href = link.url;
        linkElement.target = "_blank";
    
        const linkCardElement = document.createElement("div");
        linkCardElement.className = "link-card";
    
        const linkTitleElement = document.createElement("div");
        linkTitleElement.className = "link-title";
        linkTitleElement.textContent = link.title;
    
        const linkDescElement = document.createElement("div");
        linkDescElement.className = "link-desc";
        linkDescElement.textContent = link.desc;
    
        linkCardElement.appendChild(linkTitleElement);
        linkCardElement.appendChild(linkDescElement);
    
        linkElement.appendChild(linkCardElement);
        projectInfoElement.appendChild(linkElement);
    });
}

// 获取7天的IP数据
function get7DaysIpData() {
    
    $.ajax({
        type: "POST",
        url: "./get7DaysIpData.php",
        success: function(res){

            // 200状态码
            if(res.code == 200){
                
                // 表头
                var $thead_HTML = $(
                    '<tr>' +
                    '    <th>日期</th>' +
                    '    <th>群活码</th>' +
                    '    <th>客服码</th>' +
                    '    <th>渠道码</th>' +
                    '    <th>短网址</th>' +
                    '    <th>中间页</th>' +
                    '    <th>分享卡片</th>' +
                    '    <th>多项单页</th>' +
                    '</tr>'
                );
                $("#right .ipDataList thead").html($thead_HTML);
                
                for (var i=0; i<res.sevenDaysIpData.length; i++) {
                    
                    var $tbody_HTML = $(
                        '<tr>' +
                        '   <td>'+res.sevenDaysIpData[i].ip_create_time+'</td>' +
                        '   <td>'+res.sevenDaysIpData[i].qun_ip+'</td>' +
                        '   <td>'+res.sevenDaysIpData[i].kf_ip+'</td>' +
                        '   <td>'+res.sevenDaysIpData[i].channel_ip+'</td>' +
                        '   <td>'+res.sevenDaysIpData[i].dwz_ip+'</td>' +
                        '   <td>'+res.sevenDaysIpData[i].zjy_ip+'</td>' +
                        '   <td>'+res.sevenDaysIpData[i].shareCard_ip+'</td>' +
                        '   <td>'+res.sevenDaysIpData[i].multiSPA_ip+'</td>' +
                        '</tr>'
                    );
                    $("#right .ipDataList tbody").append($tbody_HTML);
                }
            }else{
                
                // 未登录
                if(res.code == 201){
                    
                    // 跳转到登录页面
                    jumpUrl('../login/');
                }
                
                // 无法获取到IP数据
                $('#right .ipDataList').html('<span>'+res.msg+'</span>');
            }
            
      },
      error: function(){
        
        // 发生错误
        errorPage('getipData.php')
      },
    });
}

// 错误页面
function errorPage(text){
    $("#right .data-card .data-chart").html(
    '<div class="errorPage">' +
    '   <img src="../../static/img/errorIcon.png"/><br/>' +
    '   <p class="errorText">服务器发生错误！<br/>可按F12打开开发者工具点击Network或网络查看'+text+'的返回信息进行排查！<br/>'+
    '   <a href="../../static/img/tiaoshi.jpg" target="_blank">示例图</a></p>' +
    '</div>'
    );
}

// 无数据
function noData(text){
    $("#right .data-card .data-chart").html(
    '<div class="warnPage">' +
    '   <img src="../../static/img/noData.png"/><br/>' +
    '   <p class="warnText">'+text+'</p>' +
    '</div>'
    );
}

// 获取URL参数
function queryURLParams(url) {
    var pattern = /(\w+)=(\w+)/ig;
    var parames = {};
    url.replace(pattern, ($, $1, $2) => {
        parames[$1] = $2;
    });
    return parames;
}

// 跳转到指定路径
function jumpUrl(jumpUrl){
    
    // 1秒后跳转至jumpUrl
    setTimeout('location.href="'+jumpUrl+'"',1000);
}

console.log('%c 欢迎使用引流宝','color:#3B5EE1;font-size:30px;font-family:"微软雅黑"');
console.log('%c 作者：TANKING','color:#3B5EE1;font-size:30px;font-family:"微软雅黑"');
console.log('%c 作者博客：https://segmentfault.com/u/tanking','color:#3B5EE1;font-size:30px;font-family:"微软雅黑"');
console.log('%c 开源地址：https://github.com/likeyun/liKeYun_Ylb','color:#3B5EE1;font-size:30px;font-family:"微软雅黑"');