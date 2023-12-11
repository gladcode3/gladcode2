start = True
def loop():
    global start
    if start:
        if moveTo(12.5,12.5):
            start = False
     
    if getLowHp():
        if doYouSeeMe():
            turnToTarget()
            stepLeft()
         
        else:
            if getAmbushTimeLeft() > 0:
                if isStunned() and not doYouSeeMe():
                    assassinate(getTargetX(), getTargetY())
                elif getAmbushTimeLeft() <= 1:
                    attackRanged(getTargetX(), getTargetY())
             
            else:
                ambush()
         
     
    elif not start:
        turn(50)
 