#Import libraries
import paho.mqtt.client as mqtt
import mysql.connector,time
import datetime

#Connect to online database
mydb = mysql.connector.connect(
      host="iot-db.cpylzoycvxza.ap-southeast-1.rds.amazonaws.com",
      user="admin",
      password="lab-password",
      database="sensordb"
)

#Function to subscribe to topics on connection 
def on_connect(client,userdata,flags,rc):
    print("Connected with result code "+str(rc))
    
    client.subscribe([("rpi_2", 1), ("rpi_1", 1),("rpi_3",1)]);

#Function to process messages from subscribed topics
def on_message(client,userdata,msg):

    #Process message
    val = str(msg.payload.decode("utf-8"))
    val = val.split(",")
    try:
        temp = val[0]
        light = val[1]
        ts = str(datetime.datetime.now())

        #Generate database cursor to interact with online database
        mycursor = mydb.cursor()
        
        #SQL to insert data
        sql = "INSERT INTO sensordb VALUES (NULL, '"+msg.topic+"','"+temp+"','"+light+"','"+ts+"')" #modify later for temp and light

        #Execute SQL query
        mycursor.execute(sql)

        #Commit changes to database
        mydb.commit()

        #Used for debugging
        print(msg.topic+" temperature is "+temp+" light is "+light)
    except:
        print("Error error error")

#Generate MQTT Client for MQTT use
client = mqtt.Client()

#Set on_connect function as declared earlier
client.on_connect = on_connect

#Set on_message function as declared earlier
client.on_message = on_message

#Open connection to MQTT Broker IP to receive messages
client.connect("192.168.43.137",1883,60)

#Infinite loop to receive messages
client.loop_forever()
