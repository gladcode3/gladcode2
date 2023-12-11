def loop():
    if getLastHitTime() <= 2:
        stepLeft()
    elif getCloseEnemy():
        attackRanged(getTargetX(), getTargetY())
    else:
        turnLeft(5)
 