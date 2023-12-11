def loop():
    if getHit():
        turnToLastHit()
        if howManyEnemies() == 1 and getLowHp() and getTargetHealth() <= 0.3:
            attackRanged(getTargetX(), getTargetY())
        else:
            while howManyEnemies() > 1:
                stepBack()
         
     
    else:
        while not getHit() and not moveTo(20,5):
            pass
        while not getHit() and not moveTo(5,20):
            pass
     
 