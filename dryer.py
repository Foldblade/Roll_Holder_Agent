# !/usr/bin/python3
# encoding:utf-8

import RPi.GPIO as GPIO
import os
import json
import time
import ultrasonic

where_script = os.path.split(os.path.realpath(__file__))[0]
f = open(where_script + '/.config.json', 'r') # 在此文件内修改你的GPIO配置
configjson = json.load(f)
f.close()

time.sleep(3) # 开机后保险起见，等待3s

relay_channel = configjson["GPIO"]["relay"]

GPIO.setmode(GPIO.BCM)
GPIO.setup(relay_channel, GPIO.OUT)

'''
若红外对射输入为0/False，则有人
若红外对射输入为1/True，则没人
'''
def dryer(name):
    while True:
        newmeasure = ultrasonic.measure(2)
        print(newmeasure)
        if newmeasure > 15:
            input_state = False
        else :
            input_state = True
        
        if input_state == True:
            print("[烘干机]\t有人")
            GPIO.output(relay_channel, True)
            print("[烘干机]\t继电器置True")
            print("[烘干机]\ttime sleep 0.2")
            time.sleep(0.2)
            GPIO.output(relay_channel, False)
            print("[烘干机]\t继电器置False")
        else:
            print("[烘干机]\t没人")
            GPIO.output(relay_channel, False)
            print("[烘干机]\t继电器置False")
        time.sleep(2)

if __name__ == "__main__":
    time.sleep(10)
    dryer('DRYER')