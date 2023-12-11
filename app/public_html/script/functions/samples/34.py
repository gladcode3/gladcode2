start = True
def loop():
    global start
    if start:
        while not moveTo(7.5,12.5):
            pass
        turnTo(12.5,12.5)
        start = False
     
    moveForward(10)
    turn(180)
 