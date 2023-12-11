import socket
import sys
from time import sleep

soc = False

def initClient():
    while True:
        global soc
        host = "127.0.0.1"
        port = 8888

        try:
            soc = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            soc.connect((host, port))
            break
        except:
            # print("Connection error")
            sleep(0.01)

    # print("Connected")

def startSim():
    return bool(int(sendMessage("startSimulation")))

def sendMessage(message):
    global soc
    #send data
    try:
        soc.sendall(message.encode("utf8"))
    except:
        pass

    # print("Data sent")
    #Receive a reply from the server
    server_reply = soc.recv(5120).decode("utf8")
    # print("Data received: ", server_reply)
    if server_reply == "":
        return 0
    else:
        return server_reply

def running():
    return bool(int(sendMessage("isSimRunning")))

def endSocketComm():
    sendMessage("endSocketComm")

