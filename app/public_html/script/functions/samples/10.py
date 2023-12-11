start = True
def loop():
    global start
    if start:
        if moveTo(12.5,12.5):
            start = False
     
    elif getHit():
        if getBurnTimeLeft() > 0:
            teleport(0,0)
        else:
            ambush()
            turnToLastHit()
     
    elif not start:
        turn(50)
 