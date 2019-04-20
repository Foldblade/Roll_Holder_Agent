# !/usr/bin/python3
# encoding:utf-8

import temp_new
import ultrasonic
import airQuality
import mysql
import os
import json
import time

where_script = os.path.split(os.path.realpath(__file__))[0]
f = open(where_script + '/.config.json', 'r') # 在此文件内修改你的GPIO配置
configjson = json.load(f)
f.close()

def noPaperAlert() :
    AQ = airQuality.get()
    humiture_dict = temp_new.get()
    thickness = 8.5 # 手动给一个缺纸量，达到报警效果
    try:
        mysql.data_upload(humiture_dict['temperature'], humiture_dict['humidity'], thickness, AQ, configjson["location"], configjson["number"])
        print('Upload log success!')
    except Exception as e:
        print(e, 'Someting went wrong.')

def log(name) :
    thickness_previous = -1
    while True:
        time.sleep(60)
        # print('AQ:\t', airQuality.get()) # 测试，空气质量
        # print('H/T:\t', temp_new.get()) # 测试，湿度&温度

        AQ = airQuality.get()
        humiture_dict = temp_new.get()
        thickness = ultrasonic.measure()
        if thickness_previous == -1:
            thickness_previous = thickness  # 首次运行，存储厚度数据
        else : 
            if thickness_previous - thickness > 1: # 纸张厚度变化1cm
                print('Paper added!')
                try:
                    mysql.paperChange(configjson["location"], configjson["number"])
                except Exception as e:
                    print(e, 'Someting went wrong.')
        try:
            mysql.data_upload(humiture_dict['temperature'], humiture_dict['humidity'], thickness, AQ, configjson["location"], configjson["number"])
            print('Upload log success!')
        except Exception as e:
            print(e, 'Someting went wrong.')
    
if __name__ == "__main__":
    log('LOG')