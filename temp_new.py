# !/usr/bin/python3
# encoding:utf-8

import Adafruit_DHT
import json
import os

# 读取config文件内容
where_script = os.path.split(os.path.realpath(__file__))[0]
f = open(where_script + '/.config.json', 'r') # 在此文件内修改你的GPIO配置
configjson = json.load(f)
f.close()

sensor = Adafruit_DHT.DHT11 # 我们采用的是DHT11
pin = configjson["GPIO"]["humiture"] # DHT11的GPIO引脚

def get():
    '''get：
    通过Adafruit_DHT库获取DHT11的温湿度并返回。
        
        Example：
            get()
        
        Returns:
            成功返回一个字典(Dictionary)，失败返回0。
            Example: 
                Success:
                {
                    'temperature': 21.0, 
                    'humidity': 82.0
                }
                Failed:
                0
            
        
    '''
    humidity, temperature = Adafruit_DHT.read_retry(sensor, pin)
    if humidity is not None and temperature is not None:
        # print('Temp={0:0.1f}*  Humidity={1:0.1f}%'.format(temperature, humidity))
        return {'temperature': temperature, 'humidity': humidity}
    else:
        # print('Failed to get reading. Try again!')
        return 0

if __name__ == '__main__': # 测试
    print(get())