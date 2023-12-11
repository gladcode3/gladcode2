start = True
def loop():
    global start
    if start:
        if moveTo(12.5,12.5):
            start = False
     
    if getLowHp():
        if doYouSeeMe() and getBlockTimeLeft() <= 0:
            block()
        else:
            charge()
     
    elif not start:
        turn(50)
 