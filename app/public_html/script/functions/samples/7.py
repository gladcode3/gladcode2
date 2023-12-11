def loop():
    if getLowHp():
        if getAp() >= 40:
            fireball(getTargetX(), getTargetY())
        else:
            attackRanged(getTargetX(), getTargetY())
     
    else:
        while getX() != 12.5 or getY() != 12.5:
            teleport(12.5,12.5)
        turn(50)
     
 