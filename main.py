# !/usr/bin/python3
# encoding:utf-8

import RPi.GPIO as GPIO
import os
import json
import time
import ultrasonic
import stepper

time.sleep(3) # 开机后保险起见，等待3s

infrared_channel = 22

GPIO.setmode(GPIO.BCM)
GPIO.setup(infrared_channel, GPIO.IN)

time.sleep(3) # 开机后保险起见，等待3s

# count = 0
lock = False
while True:
    '''
    if GPIO.input(infrared_channel) == 1 and lock == True:
        count = count + 1
        print("计数：\t", count)
        print("秒：\t", count * 2)
        print("")
    else :
        count = 0
    '''
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

GPIO.cleanup()