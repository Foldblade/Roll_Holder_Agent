<?php require "./header.html" ?>
<?php

    // 登录认证部分
    require "authorize.php";
    if (check($_COOKIE["znzh_user"], $_COOKIE["znzh_passwd"]) != True) {
        header("Location: login.php?error=notloggedin");
    }

    header("Content-type:text/html;charset=utf-8"); 
    $config_json = file_get_contents(".sql_config.json");
    $sqlconfig = json_decode($config_json, true);

    $con = mysqli_connect($sqlconfig["host"], $sqlconfig["user"], $sqlconfig["password"], $sqlconfig["database"]);
    mysqli_query($con, "SET NAMES UTF8"); 

    if (!$con) {
      die('Could not connect: '.mysqli_error());
    }

    $devices = mysqli_fetch_all(mysqli_query($con, "SELECT location, number FROM `shuju`")); // 写死了的shuju表

    // print_r($devices);

    $locations = array(); //  获取地址（们）
    foreach ($devices as $device) {
        if (!in_array($device[0], $locations)) {
            array_push($locations, $device[0]);
        }
    }

    // print_r($locations);
?>
<!--选项-->
<div class="container">
    <div class="row">
        <div class="card-panel white col s12 m8 offset-m2">
            <p></p>
            <div class="row">
                
                <form action="detail.php" method="get" enctype="multipart/form-data">
                    <div class="input-field col s8 m6 offset-m2">
                        <select name="deviceID">
                            <?php
                                foreach ($locations as $location) {
                                    echo '<optgroup label="'.$location.'">';
                                    foreach ($devices as $device) {
                                        if ($device[0] == $location) {
                                            echo '<option value="'.$device[1].'">'.$device[1].'</option>';
                                        }
                                    }
                                    echo '</optgroup>';
                                }
                            ?>
                        </select>
                        <label>设备列表</label>
                    </div>
                    <div class="input-field col m2 s4">
                        <button class="btn waves-effect waves-light red lighten-2 btn-large" type="submit" id="search">查询
                            <i class="material-icons right">search</i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="fixed-action-btn">
        <a class="btn-floating btn-large red">
            <i class="large material-icons">more_horiz</i>
        </a>
        <ul>
            <li><a class="btn-floating red" onClick="javascript :history.back(-1);"><i class="material-icons">arrow_back</i></a></li>
            <li><a class="btn-floating green" href="#search"><i class="material-icons">search</i></a></li>
            <li><a class="btn-floating blue" href="#"><i class="material-icons">publish</i></a></li>
        </ul>
    </div>
      

    <?php
        if (!array_key_exists("deviceID", $_GET)) {
        // if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        // if($_POST["deviceID"] == '') {
            $sql = "SELECT * FROM `shuju`"; // 写死了的shuju表
            $alldata = mysqli_fetch_all(mysqli_query($con, $sql), MYSQLI_ASSOC);
            // print_r($alldata);
            $left_less_str = "";
            $AQ_bad_str = "";
            foreach($alldata as $data) {
                $datetime = $data["datetime"];
                $temperature = $data["temperature"];
                $humidness = $data["humidness"];
                $location = $data["location"];
                $number = $data["number"];
                // print_r($data);
                if ($data["thickness"] > 7.5) { // $data["thickness"]为传感器到纸张的距离
                    $left = '<span class="red-text">纸量不足</span>';
                    $left_less_str = $left_less_str.$location."-".$number.' ';
                } else {
                    $left = "纸量尚可";
                }
                if ($data["smelly"] == 0) { // $data["smelly"]为空气质量，1为好，0为不好
                    $AQ = '<span class="red-text">空气质量糟糕</span>';
                    $AQ_bad_str = $AQ_bad_str.$location-$number.' ';
                } else {
                    $AQ = "空气质量尚可";
                }
                echo <<< EOF
                <div class="row">
                    <div class="col s12 m8 offset-m2">
                        <div class="card white">
                            <div class="card-content black-text">
                                <span class="card-title">$location-$number</span>
                                <p><i class="fas fa-clock"></i> 更新时间：$datetime</p>
                                <p><i class="fas fa-thermometer-half"></i> 温度：$temperature ℃</p>
                                <p><i class="fas fa-tint"></i> 湿度：$humidness %</p>
                                <p><i class="fas fa-toilet-paper"></i> 剩余纸量：$left</p>
                                <p><i class="fas fa-wind"></i> 空气质量：$AQ</p>
                                <p><i class="fas fa-info-circle"></i> <a href="?deviceID=$number">点此查看详细信息</a></p>
                            </div>
                        </div>
                    </div>
                </div>
EOF;
            }
            if ($left_less_str != "" or $AQ_bad_str != "") {
                if ($left_less_str != "" and $AQ_bad_str != "") {
                    $alert_text = '<span class="red-text">'.$left_less_str.'</span><b>纸张余量不足</b>，<br />
                                   <span class="red-text">'.$AQ_bad_str.'</span><b>空气质量不佳</b>!<br />';
                } else {
                    if ($left_less_str != "") {
                        $alert_text = '<span class="red-text">'.$left_less_str.'</span><b>纸张余量不足</b>！';
                    }
                    if ($AQ_bad_str != "") {
                        $alert_text = '<span class="red-text">'.$AQ_bad_str.'</span>，<b>空气质量不佳</b>！';
                    }
                }
                echo <<< EOF
                <a class="waves-effect waves-light btn modal-trigger hide" href="#AlertModal">Modal</a>
                <div id="AlertModal" class="modal">
                    <div class="modal-content">
                        <h4>温馨提醒</h4>
                        <p>请注意，$alert_text</p>
                    </div>
                    <div class="modal-footer">
                        <a class="modal-close waves-effect waves-green btn-flat">好的</a>
                    </div>
                </div>
EOF;
            }
        } else if (array_key_exists("deviceID", $_GET)) { // 单个查询结果
            $seleted_device = $_GET["deviceID"];
            $sql = "SELECT * FROM `shuju` WHERE number=".$seleted_device; // 写死了的shuju表
            $data = mysqli_fetch_assoc(mysqli_query($con, $sql));
            // print_r($data);
            $datetime = $data["datetime"];
            $temperature = $data["temperature"];
            $humidness = $data["humidness"];
            $location = $data["location"];
            $number = $data["number"];
            
            if ($data["thickness"] > 7.5) { // $data["thickness"]为传感器到纸张的距离
                $left = '<span class="red-text">纸量不足</span>';
                $left_flag = True;
            } else {
                $left = "纸量尚可";
                $left_flag = False;
            }
            if ($data["smelly"] == 0) { // $data["smelly"]为空气质量，1为好，0为不好
                $AQ = '<span class="red-text">空气质量糟糕</span>';
                $AQ_flag = True;
            } else {
                $AQ = "空气质量尚可";
                $AQ_flag = False;
            }
            echo <<< EOF
            <div class="row">
                <div class="col s12 m8 offset-m2">
                    <div class="card white">
                        <div class="card-content black-text">
                            <span class="card-title">$location-$number</span>
                            <p><i class="fas fa-clock"></i> 更新时间：$datetime</p>
                            <p><i class="fas fa-thermometer-half"></i> 温度：$temperature ℃</p>
                            <p><i class="fas fa-tint"></i> 湿度：$humidness %</p>
                            <p><i class="fas fa-toilet-paper"></i> 剩余纸量：$left</p>
                            <p><i class="fas fa-wind"></i> 空气质量：$AQ</p>
                        </div>
                    </div>
                </div>
            </div>
EOF;
            if ($left_flag or $AQ_flag) {
                if ($left_flag and $AQ_flag) {
                    $alert_text = '<span class="red-text">纸张余量不足、空气质量不佳</span>！';
                } else {
                    if ($left_flag) {
                        $alert_text = '<span class="red-text">纸张余量不足</span>！';
                    }
                    if ($AQ_flag) {
                        $alert_text = '<span class="red-text">空气质量不佳</span>！';
                    }
                }
                echo <<< EOF
                <a class="waves-effect waves-light btn modal-trigger hide" href="#AlertModal">Modal</a>
                <div id="AlertModal" class="modal">
                    <div class="modal-content">
                        <h4>警报</h4>
                        <p>请注意，<b>$location-$number</b>号机器的$alert_text</p>
                    </div>
                    <div class="modal-footer">
                        <a class="modal-close waves-effect waves-green btn-flat">我知道了</a>
                    </div>
                </div>
EOF;
                // 图表预留位
                echo <<< EOF
                <div class="row">
                    <div class="col s12 m8 offset-m2">
                        <div class="card white">
                            <div id="chart_temperature_humidness" style="width: 600px;height:600px;"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 m8 offset-m2">
                        <div class="card white">
                            <div id="chart_paper_useage" style="width: 600px;height:300px;"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 m8 offset-m2">
                        <div class="card white">
                            <div id="chart_paper_useage_hot" style="width: 600px;height:250px;"></div>
                        </div>
                    </div>
                </div>
EOF;
            }
        }
    ?>
                
</div>
<?php mysqli_close($con); ?>

<!-- JavaScript绘图区 -->
<script type="text/javascript">
// 基于准备好的dom，初始化echarts实例
var myChart = echarts.init(document.getElementById('chart_temperature_humidness'))
// 指定图表的配置项和数据
var timeData = [
    '2019-03-29 16:59:41', '2019-03-29 17:00:31', '2019-03-29 17:01:45', '2019-03-29 17:02:51', '2019-03-29 17:03:24', '2019-03-29 17:04:28'
];

option = {
    title: {
        text: '温湿度数据',
        x: 'center'
    },
    tooltip: {
        trigger: 'axis',
        axisPointer: {
            animation: false
        }
    },
    legend: {
        data:['流量','降雨量'],
        x: 'left'
    },
    toolbox: {
        feature: {
            dataZoom: {
                yAxisIndex: 'none'
            },
            restore: {},
            saveAsImage: {}
        }
    },
    axisPointer: {
        link: {xAxisIndex: 'all'}
    },
    dataZoom: [
        {
            show: true,
            realtime: true,
            start: 65,
            end: 100,
            xAxisIndex: [0, 1]
        },
        {
            type: 'inside',
            realtime: true,
            start: 65,
            end: 100,
            xAxisIndex: [0, 1]
        }
    ],
    grid: [{
        left: 50,
        right: 50,
        height: '35%'
    }, {
        left: 50,
        right: 50,
        top: '55%',
        height: '35%'
    }],
    xAxis : [
        {
            type : 'category',
            boundaryGap : false,
            axisLine: {onZero: true},
            data: timeData
        },
        {
            gridIndex: 1,
            type : 'category',
            boundaryGap : false,
            axisLine: {onZero: true},
            data: timeData,
            position: 'top'
        }
    ],
    yAxis : [
        {
            name : '温度(℃)',
            type : 'value',
            scale : true
        },
        {
            gridIndex: 1,
            name : '湿度(%)',
            type : 'value',
            inverse: true,
            scale : true
        }
    ],
    series : [
        {
            name:'温度',
            type:'line',
            symbolSize: 8,
            hoverAnimation: false,
            data:[
                24.0, 24.2, 24.0, 24.5, 25.0, 25.0
            ]
        },
        {
            name:'湿度',
            type:'line',
            xAxisIndex: 1,
            yAxisIndex: 1,
            symbolSize: 8,
            hoverAnimation: false,
            data: [
                81, 80, 78, 77, 77, 76
            ]
        }
    ]
};

// 使用刚指定的配置项和数据显示图表。
myChart.setOption(option);

var myChart2 = echarts.init(document.getElementById('chart_paper_useage'))

option2 = {
    title: {
        top: 30,
        left: 'center',
        text: '本周纸张更换量'
    },
    tooltip: {
        trigger: 'axis',
        axisPointer: {
            animation: false
        }
    },
    xAxis: {
        type: 'category',
        data: ['2019-03-25', '2019-03-26', '2019-03-27', '2019-03-28', '2019-03-29', '2019-03-30', '2019-03-31']
    },
    yAxis: {
        type: 'value'
    },
    series: [{
        data: [1, 2, 1, 2, 3, 1, 0],
        type: 'bar'
    }]
};

myChart2.setOption(option2);

// 基于准备好的dom，初始化echarts实例
var myChart3 = echarts.init(document.getElementById('chart_paper_useage_hot'))

function getVirtulData(year) {
    year = year || '2018';
    var date = +echarts.number.parseDate(year + '-01-01');
    var end = +echarts.number.parseDate((+year + 1) + '-01-01');
    var dayTime = 3600 * 24 * 1000;
    var data = [];
    for (var time = date; time < end; time += dayTime) {
        data.push([
            echarts.format.formatTime('yyyy-MM-dd', time),
            Math.floor(Math.random()*4)
        ]);
    }
    return data;
}

option3 = {
    title: {
        top: 30,
        left: 'center',
        text: '2018年纸张更换量'
    },
    toolbox: {
        feature: {
            dataZoom: {
                yAxisIndex: 'none'
            },
            restore: {},
            saveAsImage: {}
        }
    },
    tooltip : {},
    visualMap: {
        min: 0,
        max: 3,
        splitNumber:3,
        type: 'piecewise',
        orient: 'horizontal',
        left: 'center',
        top: 65,
        textStyle: {
            color: '#000'
        }
    },
    calendar: {
        top: 120,
        left: 30,
        right: 30,
        cellSize: ['auto', 13],
        range: '2018',
        itemStyle: {
            normal: {borderWidth: 0.5}
        },
        yearLabel: {show: false}
    },
    series: {
        type: 'heatmap',
        coordinateSystem: 'calendar',
        data: getVirtulData(2018)
    }
};

myChart3.setOption(option3);
    </script>
<?php require "./footer.html" ?>