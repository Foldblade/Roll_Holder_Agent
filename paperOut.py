# !/usr/bin/python3
# encoding:utf-8

import RPi.GPIO as GPIO
import os
import json
import time
import ultrasonic
import stepper

time.sleep(3) # 开机后保险起见，等待3s

where_script = os.path.split(os.path.realpath(__file__))[0]
f = open(where_script + '/.config.json', 'r') # 在此文件内修改你的GPIO配置
configjson = json.load(f)
f.close()

infrared_channel = configjson["GPIO"]["infrared"]

GPIO.setmode(GPIO.BCM)
GPIO.setup(infrared_channel, GPIO.IN)

time.sleep(3) # 开机后保险起见，等待3s

def paperOut(name):
    # 单纯的红外对射出纸方案
    while True:
        if GPIO.input(infrared_channel) == False: # 有人
            print("[出纸]\t红外对射有人-False")
            left_length = ultrasonic.measure()
            print(left_length)
            if left_length >= 10.1:
                left_length == 9
            # 算法
            angle = 35 / (10.1 - left_length) # 角度
            turns = angle / 3.18 # 圈数
            # 手在，出纸
            stepper.forward(3 / 1000.0, int(turns * 50))
            print("[出纸]\t开始5秒冷却")
            time.sleep(5)
            print("[出纸]\t5秒冷却结束")
        else:
            print("[出纸]\t红外对射没人-True")
            time.sleep(0.2)
    
'''
    # 原来的红外传感器方案
    lock = False
while True:
    print("人：\t", GPIO.input(infrared_channel))
    print("Lock：\t", lock)
    if GPIO.input(infrared_channel) == 1 and lock == False:
        time.sleep(1)
    if GPIO.input(infrared_channel) == 1 and lock == False:
        lock = True # 上锁
        left_length = ultrasonic.measure()
        print(left_length)
        # 算法
        angle = 35 / (10.1 - left_length) # 角度
        turns = angle / 3.18 # 圈数
        # 手在，出纸
        stepper.forward(3 / 1000.0, int(turns * 50))
    if GPIO.input(infrared_channel) == 0 and lock == True:
        time.sleep(0.2)
    if GPIO.input(infrared_channel) == 0 and lock == True:
        lock = False
    time.sleep(0.5)
'''

if __name__ == "__main__":
    paperOut('PAPEROUT')