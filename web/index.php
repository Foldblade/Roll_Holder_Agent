<?php require "./header.html" ?>
<?php
    header("Content-type:text/html;charset=utf-8"); 
    $config_json = file_get_contents(".sql_config.json");
    $sqlconfig = json_decode($config_json, true);

    $con = mysqli_connect($sqlconfig["host"], $sqlconfig["user"], $sqlconfig["password"], $sqlconfig["database"]);
    mysqli_query($con, "SET NAMES UTF8"); 

    if (!$con) {
      die('Could not connect: ' . mysqli_error());
    }

    $devices = mysqli_fetch_all(mysqli_query($con, "SELECT location, number FROM `shuju`")); // 写死了的shuju表

    // print_r($devices);

    $locations = array();
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
                
                <form action="index.php" method="post" enctype="multipart/form-data">
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
                        <button class="btn waves-effect waves-light btn-large" type="submit" name="action">查询
                            <i class="material-icons right">search</i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
        if($_SERVER['REQUEST_METHOD'] != 'POST') {
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
                        <a href="#!" class="modal-close waves-effect waves-green btn-flat">好的</a>
                    </div>
                </div>
EOF;
            }
        } else {
            $seleted_device = $_POST["deviceID"];
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
                        <a href="#!" class="modal-close waves-effect waves-green btn-flat">我知道了</a>
                    </div>
                </div>
EOF;
            }
        }
    ?>
</div>
<?php mysqli_close($con); ?>
<?php require "./footer.html" ?>