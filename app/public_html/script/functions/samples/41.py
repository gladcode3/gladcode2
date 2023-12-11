def loop():
    if getLastHitTime() <= 2:
        stepRight()
    elif getCloseEnemy():
        attackRanged(getTargetX(), getTargetY())
    else:
        turnRight(5)
 