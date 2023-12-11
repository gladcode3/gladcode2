'''
contém as funções que podem ser chamadas diretamente pelo usuário.
Estas funções somente enviam sinais do cliente para o servidor. O servidor é quem executa as funções e devolve a resposta.
Desta maneira somente com a sintaxe dos sinais, pode-se fazer o port facilmente para qualquer linguagem.
'''

from gladCodeCore import *

def getSimTime():
    return float(sendMessage("getSimTime"))

def setSTR(arg):
    sendMessage("setSTR {}".format(arg))

def getSTR():
    return int(sendMessage("getSTR"))

def setAGI(arg):
    sendMessage("setAGI {}".format(arg))

def getAGI():
    return int(sendMessage("getAGI"))

def setINT(arg):
    sendMessage("setINT {}".format(arg))

def getINT():
    return int(sendMessage("getINT"))

def setName(name):
    name = name.replace(" ", "#")
    sendMessage("setName {}".format(name))

def getName():
    return sendMessage("getName")

def upgradeSTR(n):
    return bool(int(sendMessage("upgradeSTR {}".format(n))))

def upgradeAGI(n):
    return bool(int(sendMessage("upgradeAGI {}".format(n))))

def upgradeINT(n):
    return bool(int(sendMessage("upgradeINT {}".format(n))))

def stepForward():
    return float(sendMessage("stepForward"))

def stepBack():
    return float(sendMessage("stepBack"))

def stepLeft():
    return float(sendMessage("stepLeft"))

def stepRight():
    return float(sendMessage("stepRight"))

def turnLeft(ang):
    return float(sendMessage("turnLeft {}".format(ang)))

def turnRight(ang):
    return float(sendMessage("turnRight {}".format(ang)))

def turn(ang):
    sendMessage("turn {}".format(ang))

def turnTo(x, y):
    return bool(int(sendMessage("turnTo {} {}".format(x,y))))

def turnToTarget():
    return bool(int(sendMessage("turnToTarget")))

def turnToAngle(ang):
    return bool(int(sendMessage("turnToAngle {}".format(ang))))

def moveForward(p):
    sendMessage("moveForward {}".format(p))

def moveTo(x, y):
    return bool(int(sendMessage("moveTo {} {}".format(x,y))))

def moveToTarget():
    return bool(int(sendMessage("moveToTarget")))

def getX():
    return float(sendMessage("getX"))

def getY():
    return float(sendMessage("getY"))

def getHp():
    return float(sendMessage("getHp"))

def getAp():
    return float(sendMessage("getAp"))

def getSpeed():
    return float(sendMessage("getSpeed"))

def getHead():
    return float(sendMessage("getHead"))

def getDist(x, y):
    return float(sendMessage("getDist {} {}".format(x, y)))

def getDistToTarget():
    resp = float(sendMessage("getDistToTarget"))
    if resp < 0:
        return False
    else:
        return resp

def getAngle(x, y):
    return float(sendMessage("getAngle {} {}".format(x, y)))


def howManyEnemies():
    return int(sendMessage("howManyEnemies"))

def getCloseEnemy():
    return bool(int(sendMessage("getCloseEnemy")))

def getFarEnemy():
    return bool(int(sendMessage("getFarEnemy")))

def getLowHp():
    return bool(int(sendMessage("getLowHp")))

def getHighHp():
    return bool(int(sendMessage("getHighHp")))

def getTargetX():
    resp = float(sendMessage("getTargetX"))
    if resp < 0:
        return False
    else:
        return resp

def getTargetY():
    resp = float(sendMessage("getTargetY"))
    if resp < 0:
        return False
    else:
        return resp

def getTargetHealth():
    return float(sendMessage("getTargetHealth"))

def getTargetSpeed():
    resp = float(sendMessage("getTargetSpeed"))
    if resp < 0:
        return False
    else:
        return resp

def getTargetHead():
    resp = float(sendMessage("getTargetHead"))
    if resp < 0:
        return False
    else:
        return resp

def doYouSeeMe():
    return bool(int(sendMessage("doYouSeeMe")))

def isTargetVisible():
    return bool(int(sendMessage("isTargetVisible")))

def attackMelee():
    sendMessage("attackMelee")

def attackRanged(x, y):
    return bool(int(sendMessage("attackRanged {} {}".format(x, y))))

def getLastHitTime():
    return float(sendMessage("getLastHitTime"))

def getLastHitAngle():
    return float(sendMessage("getLastHitAngle"))

def turnToLastHit():
    return bool(int(sendMessage("turnToLastHit")))

def getHit():
    return bool(int(sendMessage("getHit")))

def getSafeRadius():
    return float(sendMessage("getSafeRadius"))

def isSafeHere():
    return bool(int(sendMessage("isSafeHere")))

def isSafeThere(x, y):
    return bool(int(sendMessage("isSafeThere {} {}".format(x, y))))

def fireball(x, y):
    return bool(int(sendMessage("fireball {} {}".format(x, y))))

def teleport(x, y):
    return bool(int(sendMessage("teleport {} {}".format(x, y))))

def block():
    return bool(int(sendMessage("block")))

def ambush():
    return bool(int(sendMessage("ambush")))

def assassinate(x, y):
    return bool(int(sendMessage("assassinate {} {}".format(x, y))))

def charge():
    return bool(int(sendMessage("charge")))

def getBlockTimeLeft():
    return float(sendMessage("getBlockTimeLeft"))

def getAmbushTimeLeft():
    return float(sendMessage("getAmbushTimeLeft"))

def getBurnTimeLeft():
    return float(sendMessage("getBurnTimeLeft"))

def setSpritesheet(str):
    #faz nada mesmo
    str = str

def isStunned():
    return bool(int(sendMessage("isStunned")))

def isBurning():
    return bool(int(sendMessage("isBurning")))

def isProtected():
    return bool(int(sendMessage("isProtected")))

def isRunning():
    return bool(int(sendMessage("isRunning")))

def isSlowed():
    return bool(int(sendMessage("isSlowed")))

def speak(message):
    sendMessage("speak {}".format(message))

def getLvl():
    return int(sendMessage("getLvl"))

def breakpoint(message):
    sendMessage("breakpoint {}".format(message))

def setPosition(x, y):
    sendMessage("setPosition {} {}".format(x, y))

def setHp(hp):
    sendMessage("setHp {}".format(hp))

def setAp(ap):
    sendMessage("setAp {}".format(ap))

def lvlUp(n):
    sendMessage("lvlUp {}".format(n))

def useItem(item):
    return bool(int(sendMessage("useItem {}".format(item))))

def setSlots(slots):
    sendMessage("setSlots {}".format(slots))

def isItemReady(item):
    return bool(int(sendMessage("isItemReady {}".format(item))))
