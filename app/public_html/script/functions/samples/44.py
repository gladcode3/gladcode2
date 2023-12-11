def loop():
    while not moveTo(12.5,12.5):
        pass
    if turnRight(45) >= 45:
        upgradeSTR(5)
    if getLowHp():
        attackRanged(getTargetX(), getTargetY())
 