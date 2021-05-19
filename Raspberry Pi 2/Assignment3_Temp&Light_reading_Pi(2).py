import paho.mqtt.publish as publish
import time
import serial
import datetime
import MySQLdb

device = '/dev/ttyACM0'
arduino = serial.Serial(device, 9600)

print("Publishing")
# publish.single("rpi_2","HELLO FROM RPI",hostname="192.168.0.196")
while(arduino.in_waiting == 0):
    pass
    
    line = arduino.readline()
    temp = int(line[13:15])
    light_density = int(line[16:])
    currentDate = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S') #format to remove micro seconds
    print(temp)
    print(light_density)
    print(currentDate)
    
    
    #conditional rule starts here
    if(light_density <= 150):
        arduino.write(b"1\r\n")
        
    if(temp == 37):
        arduino.write(b"3\r\n")
        
    mqtt_string = str(temp) + "," + str(light_density)
    
    publish.single("rpi_2",mqtt_string,hostname="192.168.43.137")

