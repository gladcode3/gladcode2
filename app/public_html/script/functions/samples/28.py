def loop():
    if getCloseEnemy():
        if isProtected() or getDistToTarget() < 2:
            stepBack()
        else:
            attackRanged(getTargetX(), getTargetY())
     
    else:
        turn(50)
 