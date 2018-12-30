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
    temperature = str(temperature)
    humidness = str(humidness)
    thickness = str(thickness)
    smelly = str(smelly)
    number = str(number)
    # 打开数据库连接
    db = pymysql.connect(sqljson["host"], sqljson["user"], sqljson["password"], sqljson["database"] )
    
    # 使用cursor()方法获取操作游标 
    cursor = db.cursor()
    
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