def loop():
    if getCloseEnemy():
        if isRunning() or getDistToTarget() < 2:
            teleport(0,0)
        else:
            fireball(getTargetX(), getTargetY())
     
    elif not getHit():
        turn(5)
        stepForward()
     
    else:
        turnToLastHit()
 