#! usr/bin/python3
import RPi.GPIO as GPIO
# import time
import os
import json

GPIO.setwarnings(False)

# 读取config文件内容
where_script = os.path.split(os.path.realpath(__file__))[0]
f = open(where_script + '/.config.json', 'r') # 在此文件内修改你的GPIO配置
configjson = json.load(f)
f.close()

channel = configjson["GPIO"]["airQuality"] # MQ-135的GPIO引脚


def get():
    GPIO.setmode(GPIO.BCM) # BCM编码模式
    GPIO.setup(channel, GPIO.IN)
    return GPIO.input(channel)


if __name__ == '__main__': # 测试
    print(get())
    GPIO.cleanup()