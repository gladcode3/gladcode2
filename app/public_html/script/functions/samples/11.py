def loop():
    if getLastHitTime() <= 2.0:
        stepLeft()
    elif getCloseEnemy():
        attackRanged(getTargetX(), getTargetY())
    else:
        turnLeft(5)
 