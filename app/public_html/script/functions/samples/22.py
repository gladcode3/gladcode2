def loop():
    if getHit():
        turnToLastHit()
        if getCloseEnemy():
            attackRanged(getTargetX(), getTargetY())
        else:
            diff = 0
            st = getSimTime()
            while diff < 4 and howManyEnemies() == 0:
                diff = getSimTime() - st
                stepBack()
     
    elif getCloseEnemy():
        attackRanged(getTargetX(), getTargetY())
    elif moveTo(12.5, 12.5):
        turn(45)
 