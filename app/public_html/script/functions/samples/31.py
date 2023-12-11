start = True
def loop():
    global start
    if getLowHp():
        d = getDistToTarget()
        if not isSlowed() and d > 2:
            charge()
        elif d <= 1:
            attackMelee()
        else:
            moveToTarget()
     
    elif start or not isSafeHere():
        if moveTo(12.5,12.5):
            start = False
     
    else:
        turn(5)
 