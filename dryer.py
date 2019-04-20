# !/usr/bin/python3
# encoding:utf-8

import RPi.GPIO as GPIO
import os
import json
import time

# 继电器带着烘干机。原heating端口废弃。

where_script = os.path.split(os.path.realpath(__file__))[0]
f = open(where_script + '/.config.json', 'r') # 在此文件内修改你的GPIO配置
configjson = json.load(f)
f.close()

infrared2_channel = configjson["GPIO"]["infrared2"]
relay_channel = configjson["GPIO"]["relay"]

GPIO.setmode(GPIO.BCM)
GPIO.setup(infrared2_channel, GPIO.IN)
GPIO.setup(relay_channel, GPIO.OUT)

'''
若红外对射输入为0/False，则有人
若红外对射输入为1/True，则没人
'''
def dryer(name):
    while True:
        input_state = GPIO.input(infrared2_channel)
        if input_state == False:
            print("[烘干机]\t红外对射有人-False")
            GPIO.output(relay_channel, True)
            print("[烘干机]\t继电器置True")
            print("[烘干机]\ttime sleep 0.2")
            time.sleep(0.2)
            GPIO.output(relay_channel, False)
            print("[烘干机]\t继电器置False")
        else:
            print("[烘干机]\t红外对射没人-True")
            GPIO.output(relay_channel, False)
            print("[烘干机]\t继电器置False")
        time.sleep(1)

if __name__ == "__main__":
    time.sleep(10)
    dryer('DRYER')