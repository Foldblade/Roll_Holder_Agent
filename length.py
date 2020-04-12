# !/usr/bin/python3
# encoding:utf-8

import RPi.GPIO as GPIO
import os
import time
import json
import ultrasonic

time.sleep(3)

where_script = os.path.split(os.path.realpath(__file__))[0]

lengthjson = json.loads("{\"length\": [7.5]}")
f = open(where_script+'/.length.json', 'w')
json.dump(lengthjson, f, indent=4, ensure_ascii=False)
f.close()

previous = 0

def do():
    global previous
    where_script = os.path.split(os.path.realpath(__file__))[0]
    f = open(where_script + '/.length.json', 'r') # 在此文件内修改你的GPIO配置
    lengthjson = json.load(f)
    f.close()

    length_log = lengthjson["length"]

    try:
        new_measure = ultrasonic.measure(1)
        while new_measure == None:
            new_measure = ultrasonic.measure(1)
            print("Measure Failed, Retry……")
        new_measure = round(new_measure, 4)
        if new_measure > 8.8:
            new_measure = 8.8
        
    except Exception as e:
        print(e)
        if len(length_log) > 1:
            new_measure = length_log[-1]
        else:
            return None
    
    # print(previous)
    if previous != 0:
        if abs(new_measure - previous) > 1:
            # 大变化，重置
            lengthjson = json.loads("{\"length\": [7.5]}")
            f = open(where_script+'/.length.json', 'w')
            json.dump(lengthjson, f, indent=4, ensure_ascii=False)
            f.close()
            previous = 0
            return None

    if len(length_log) < 7:
        print("previous:",previous, "new:", new_measure)
        length_log.append(new_measure)
        lengthjson["length"] = length_log
        f = open(where_script+'/.length.json', 'w')
        json.dump(lengthjson, f, indent=4, ensure_ascii=False)
        f.close()
        previous = new_measure
    else:
        length_log.pop(0) # 删除第一个记录
        new_measure = round(((length_log[1] + length_log[2] + length_log[3] + length_log[4] + length_log[5] + new_measure) / 6), 4)
        print("previous:",previous, "new:", new_measure)
        length_log.append(new_measure)
        lengthjson["length"] = length_log
        f = open(where_script+'/.length.json', 'w')
        json.dump(lengthjson, f, indent=4, ensure_ascii=False)
        f.close()
        previous = new_measure
    return None

if __name__ == '__main__':
    GPIO.cleanup()
    while True:
        time.sleep(5)
        do()
