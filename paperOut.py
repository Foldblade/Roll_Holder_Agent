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
time.sleep(5) # 开机后保险起见，等待5s

def paperOut(name):
    # 单纯的红外对射出纸方案
    while True:
        time.sleep(2) # 两秒测距一次
        hand_distance = ultrasonic.measure(3)
        print(hand_distance)
        if hand_distance > 15:
            input_state = False
        else :
            input_state = True

        if input_state == True: # 有人
            print("[出纸]\t有人")
            f = open(where_script + '/.length.json', 'r') # 在此文件内修改你的GPIO配置
            lengthjson = json.load(f)
            f.close()

            length_log = lengthjson["length"]
            left_length = length_log[-1]
            print(left_length)
            # 算法
            angle = 35 / (11 - left_length) # 角度
            turns = angle / 3.15 # 圈数
            # 手在，出纸
            stepper.backwards(3 / 1000.0, int(turns * 50))
            print("[出纸]\t开始5秒冷却")
            time.sleep(5)
            print("[出纸]\t5秒冷却结束")
        else:
            print("[出纸]\t没人")
            time.sleep(0.2)

if __name__ == "__main__":
    paperOut('PAPEROUT')