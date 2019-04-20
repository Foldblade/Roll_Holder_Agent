# !/usr/bin/python3
# encoding:utf-8

import RPi.GPIO as GPIO
import os
import json
import time
import log

where_script = os.path.split(os.path.realpath(__file__))[0]
f = open(where_script + '/.config.json', 'r') # 在此文件内修改你的GPIO配置
configjson = json.load(f)
f.close()

button_channel = configjson["GPIO"]["button"]

GPIO.setup(button_channel, GPIO.IN, pull_up_down=GPIO.PUD_UP)

def buttonListener(name): # 按钮监听
    while True:
        button_state = GPIO.input(button_channel)
        if button_state == False:
            print("[报警按钮]\tButton Pressed")
            print("[报警按钮]\t发送无纸伪数据")
            log.noPaperAlert()
            print("[报警按钮]\t无纸伪数据发送结束")
        else: 
            print("[报警按钮]\tButton NOT pressed")
        time.sleep(0.2)

if __name__ == "__main__":
    time.sleep(2)
    buttonListener('BUTTONLISTENER')
