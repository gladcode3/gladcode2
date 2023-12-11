def loop():
    if getLastHitTime() <= 2.0:
        stepLeft()
    elif getFarEnemy():
        attackRanged(getTargetX(), getTargetY())
    else:
        turnLeft(5)
 