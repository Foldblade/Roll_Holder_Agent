# !/usr/bin/python3
# encoding:utf-8

import RPi.GPIO as GPIO
import os
import json
import time
import ultrasonic
import stepper

infrared_channel = 22

GPIO.setmode(GPIO.BCM)
GPIO.setup(infrared_channel, GPIO.IN)

count = 0
lock = False
while True:
    if GPIO.input(infrared_channel) == 1 and lock == True:
        count = count + 1
        print("计数：\t", count)
        print("秒：\t", count * 0.5)
    else :
        count = 0
    print("人\t", GPIO.input(infrared_channel))
    print("Lock\t", lock)
    if GPIO.input(infrared_channel) == 1 and lock == False:
        left_length = ultrasonic.measure()
        print(left_length)
        # 手在，出纸
        stepper.backwards(3 / 1000.0, 150)
        lock = True
    if  GPIO.input(infrared_channel) == 0 :
        lock = False
    time.sleep(0.5)

GPIO.cleanup()