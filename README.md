# Roll_Holder_Agent（纸盒特工）

[TOC]

“纸盒特工”是我们为节能减排竞赛所做的小制作。升级自学长的毕业设计，原名智能纸盒，为竞赛改名“纸盒特工”。

## 主要功能：

- 伸手自动出定量纸，以达到节能减排之目的
- 检测厕纸剩余量，并在接近不足时通过APP通知管理人员更换
- 检测厕所空气质量并及时报警

## 所用配件与环境：

### 配件

- Raspberry Pi ZERO W
- 传感器
    - 红外
    - 空气质量（MQ-135）
    - 温湿度（DHT11）
    - 超声波
- 步进电机

### 环境

- Raspbian Stretch Lite
  Version: November 2018
  Release date: 2018-11-13
  Kernel version: 4.14
- Python 3.5.3

## 使用方法

### 基础配置

1. git clone
2. `cd Roll_Holder_Agent`
3. `cp .sql_config(origin).json .sqlconfig.json`
4. `cp .config(origin).json .config.json`
5. `nano .sqlconfig.json`或者`vi .sqlconfig.json`，修改你的数据库信息（见后）
6. `nano .config.json`或者`vi .config.json`，修改/查看GPIO引脚使用（见后）

### 环境配置

#### RPi.GPIO

```
pip3 install RPi.GPIO
```

#### Adafruit_Python_DHT

```
sudo apt-get update
sudo apt-get install build-essential python-dev
git clone https://github.com/adafruit/Adafruit_Python_DHT.git
cd Adafruit_Python_DHT
```
安装到Python3：

```
sudo python3 setup.py install
```

#### SQL

`shuju`表

```
temperature|humidness|thickness|smelly|location|number|datetime
```

`log`表

```
temperature|humidness|thickness|smelly|location|number|datetime|logid
```
### 使用

在正确地配置、连接各种元件后，运行`main.py`，这将监听红外传感器、驱动电机出纸。运行`log.py`，将会把数据上传到数据库。

`main.py`**开始前有合计6秒的延迟等待时间**。

建议结合开机启动与后台运行。

## 配置说明

`.config.json`

```
{
    "location": "教学楼一#101", // 所在地#卫生间门牌号
    "number": 1, // 编号（唯一）
    "GPIO": { // GPIO引脚使用
        "infrared": 22, // 红外
        "humiture": 17, // 温湿度（DHT11）
        "airQuality": 27, // 空气质量（MQ-135）DO口
        "ultrasonic_TRIGGER": 15, // 超声波Trigger口
        "ultrasonic_ECHO": 14 // 超声波Echo口
    }
}
```

`.sqlconfig.json`

```
{
    "host": "", // 主机
    "user": "", // 用户名
    "password": "", // 密码
    "database": "" //数据库
}
```

`stepper.py`

```
……
coil_A_1_pin = 25 
coil_A_2_pin = 24
coil_B_1_pin = 7
coil_B_2_pin = 8
# 电机的四个脚（我们并非直连电机）
……
```

`main.py`

```
# 出纸的算法
angle = 35 / (10.1 - left_length) # 角度
turns = angle / 3.18 # 圈数
```

## 鸣谢

### 引用/修改后使用的代码

源代码请移步`/quote`目录。

- stepper.py：
  取自《树莓派开发实战》(Raspberry Pi Cookbook)
  By : Simon Monk

- ultrasonic_1.py
  Author : Matt Hawkins

### 参考资料

[DHT11传感器读取数据 —— 使用Adafruit_Python_DHT](https://www.raspberrypi-spy.co.uk/2017/09/dht11-temperature-and-humidity-sensor-raspberry-pi/ )

[树莓派程序开机自启动方法总结](https://www.jianshu.com/p/86adb6d5347b )

[Linux 使用nohup……&后台运行](https://blog.csdn.net/xinluke/article/details/52493734#t6)

