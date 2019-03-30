# !/usr/bin/python3
# encoding:utf-8

import RPi.GPIO as GPIO
import os
import json
import time

time.sleep(1.5)

where_script = os.path.split(os.path.realpath(__file__))[0]
f = open(where_script + '/.config.json', 'r') # 在此文件内修改你的GPIO配置
configjson = json.load(f)
f.close()

button_channel = configjson["GPIO"]["button"]
heating_channel = configjson["GPIO"]["heating"]

GPIO.setmode(GPIO.BCM)
GPIO.setup(button_channel, GPIO.IN, pull_up_down=GPIO.PUD_UP)
GPIO.setup(heating_channel, GPIO.OUT)

GPIO.output(heating_channel, False) # 默认停止吹风

while True:
    input_state = GPIO.input(button_channel)
    if input_state == False:
        print("Button  Pressed")
        GPIO.output(heating_channel, True) # 吹风
        time.sleep(0.1)
        GPIO.output(heating_channel, False) # 停止吹风
    else: 
        print("NOT pressed")
    time.sleep(0.2)
