def loop():
    if getHit():
        turnToLastHit()
        getCloseEnemy()

        if isTargetVisible():
            attackRanged(getTargetX(), getTargetY())
     
 