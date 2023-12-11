def loop():
    if not isTargetVisible():
        getLowHp()
    else:
        if abs(getHead() - getTargetHead()) >= 90:
            stepLeft()
            turnToTarget()
         
        else:
            attackRanged(getTargetX(), getTargetY())
     
 