# !/usr/bin/python3
# encoding:utf-8

import json
import os
import pymysql

where_script = os.path.split(os.path.realpath(__file__))[0]
f = open(where_script + '/.sql_config.json', 'r') # 在此文件内修改你的数据库信息
sqljson = json.load(f)
f.close()

def data_upload(temperature, humidness, thickness, smelly, location, number):
    '''data_upload：
    上传温度、湿度、剩余厚度、气味报警、地点与编号
    若shuju表不存在该地点+编号的盒子，则Insert之；
    若shuju表已经有该地点+编号的盒子，则Update之。
    insert数据到log表。

        Args：
            temperature: 传感器获取的温度
            humidness: 传感器获取的湿度
            thickness: 传感器获取的纸张剩余厚度（cm）
            smelly: 传感器获取的卫生间空气质量（18/12/31为0/1）
            location: str，该纸盒所在的地点（比如主五）
            number: 该纸盒的编号/所在卫生间的门牌号（比如320）
        
        Example：
            data_upload(18, 55, 8.6, 1, '主五', 1)
        
    '''
    temperature = str(temperature)
    humidness = str(humidness)
    thickness = str(thickness)
    smelly = str(smelly)
    number = str(number)
    # 打开数据库连接
    db = pymysql.connect(sqljson["host"], sqljson["user"], sqljson["password"], sqljson["database"] )
    
    # 使用cursor()方法获取操作游标 
    cursor = db.cursor()
    
    # 执行SQL语句
    # 注意下一行有写死了是shuju表
    cursor.execute("SELECT * FROM `shuju` WHERE number =" + str(number) + " AND location='" + location + "'") # 这里写死了是shuju表
    data = cursor.fetchall()

    print(data)
    print(len(data))

    if len(data) != 0:
        print("Update")
        # 存在这台设备的记录
        # 注意下一行有写死了是shuju表
        sql = "UPDATE `shuju` SET" + \
        " temperature=" + str(temperature) + "," \
        " humidness=" + str(humidness) + "," \
        " thickness=" + str(thickness) + "," \
        " smelly=" + str(smelly) + \
        " WHERE number=" + number + " AND location='" + location +"'" 
        # SQL语句。注意这里字符串前面的空格。
        try:
            # 执行sql语句
            cursor.execute(sql)
            # 提交到数据库执行
            db.commit()
        except:
            # 如果发生错误则回滚
            db.rollback()
    else :
        print("Insert")
        # 不存在这台设备的记录
        # SQL 插入语句
        # 注意下一行有写死了是shuju表
        sql = "INSERT `shuju`(temperature, humidness, thickness, smelly, location, number) \
        VALUES (" + temperature + ", " + humidness + ", " + thickness + ", " + smelly + ", \
        '" + location + "', " + number + ")"
        try:
            # 执行sql语句
            cursor.execute(sql)
            # 提交到数据库执行
            db.commit()
        except:
            # 如果发生错误则回滚
            db.rollback()
    
    # 插入log表
    sql = "INSERT `log`(temperature, humidness, thickness, smelly, location, number) \
        VALUES (" + temperature + ", " + humidness + ", " + thickness + ", " + smelly + ", \
        '" + location + "', " + number + ")"
    try:
        # 执行sql语句
        cursor.execute(sql)
        # 提交到数据库执行
        db.commit()
    except:
        # 如果发生错误则回滚
        db.rollback()
    
    # 关闭数据库连接
    db.close()

def log_upload(temperature, humidness, smelly, location, number):
    '''log_upload：
    上传温度、湿度、气味报警、地点与编号到log表
    若数据库不存在该地点+编号的盒子，则Insert之；
    若数据库已经有该地点+编号的盒子，则Update之。

        Args：
            temperature: 传感器获取的温度
            humidness: 传感器获取的湿度
            thickness: 传感器获取的纸张剩余厚度（cm）
            smelly: 传感器获取的卫生间空气质量（18/12/31为0/1）
            location: str，该纸盒所在的地点（比如主五）
            number: 该纸盒的编号/所在卫生间的门牌号（比如320）
        
        Example：
            data_upload(18, 55, 8.6, 1, '主五', 1)
        
    '''
    temperature = str(temperature)
    humidness = str(humidness)
    smelly = str(smelly)
    number = str(number)
    # 打开数据库连接
    db = pymysql.connect(sqljson["host"], sqljson["user"], sqljson["password"], sqljson["database"] )
    
    # 使用cursor()方法获取操作游标 
    cursor = db.cursor()

    # 写死的log表
    sql = "INSERT `log`(temperature, humidness, smelly, location, number) \
    VALUES (" + temperature + ", " + humidness + ", " + smelly + ", \
    '" + location + "', " + number + ")"
    try:
        # 执行sql语句
        cursor.execute(sql)
        # 提交到数据库执行
        db.commit()
    except:
        # 如果发生错误则回滚
        db.rollback()
    
    # 关闭数据库连接
    db.close()

def left_upload(temperature, humidness, thickness, smelly, location, number):
    temperature = str(temperature)
    humidness = str(humidness)
    thickness = str(thickness)
    smelly = str(smelly)
    number = str(number)
    # 打开数据库连接
    db = pymysql.connect(sqljson["host"], sqljson["user"], sqljson["password"], sqljson["database"] )
    
    # 使用cursor()方法获取操作游标 
    cursor = db.cursor()
    
    # 执行SQL语句
    cursor.execute("SELECT * FROM `shuju` WHERE number =" + str(number) + " AND location='" + location + "'") # 这里写死了是shuju表
    data = cursor.fetchall()

    print(data)
    print(len(data))

    if len(data) != 0:
        print("Update")
        # 存在这台设备的记录
        # 注意下一行有写死了是shuju表
        sql = "UPDATE `shuju` SET" + \
        " temperature=" + str(temperature) + "," \
        " humidness=" + str(humidness) + "," \
        " thickness=" + str(thickness) + "," \
        " smelly=" + str(smelly) + \
        " WHERE number=" + number + " AND location='" + location +"'" 
        # SQL语句。注意这里字符串前面的空格。
        try:
            # 执行sql语句
            cursor.execute(sql)
            # 提交到数据库执行
            db.commit()
        except:
            # 如果发生错误则回滚
            db.rollback()
    else :
        print("Insert")
        # 不存在这台设备的记录
        # SQL 插入语句
        sql = "INSERT `shuju`(temperature, humidness, thickness, smelly, location, number) \
        VALUES (" + temperature + ", " + humidness + ", " + thickness + ", " + smelly + ", \
        '" + location + "', " + number + ")"
        try:
            # 执行sql语句
            cursor.execute(sql)
            # 提交到数据库执行
            db.commit()
        except:
            # 如果发生错误则回滚
            db.rollback()
    
    # 关闭数据库连接
    db.close()

if __name__ == '__main__': # 测试
    data_upload(18, 55, 8.6, 1, '主五', 1)